<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook as StripeWebhook;
use Stripe\StripeClient;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Item;

class StripeWebhookController extends Controller
{

    public function handle(Request $request)
    {
        // Cashierの推奨どおり：config/services.php のネストを参照
    $signingSecret = config('services.stripe.webhook.secret');
    $payload   = $request->getContent();
    $signature = $request->header('Stripe-Signature');

    Log::info('webhook entry', [
        'sig' => $request->header('Stripe-Signature'),
        'path' => $request->path(),
    ]);

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $signature, $signingSecret);
    } catch (\Throwable $e) {
        \Log::warning('stripe webhook verify failed', ['msg' => $e->getMessage()]);
        return response('Invalid signature', 400);
    }

    $type = $event->type ?? '';
    \Log::info('stripe webhook received', ['type' => $type, 'event_id' => $event->id ?? null]);

    if ($type !== 'checkout.session.completed') {
        return response('OK', 200);
    }

    /** @var \Stripe\Checkout\Session $session */
    $session   = $event->data->object;
    $sessionId = $session->id ?? null;
    $paid      = ($session->payment_status ?? null) === 'paid';

    \Log::info('branch: checkout.completed', [
        'session_id'     => $sessionId,
        'payment_status' => $session->payment_status ?? null,
    ]);

    // ===== 1) まず checkout_session_id から Payment/Purchase を特定 =====
    $payment  = Payment::where('checkout_session_id', $sessionId)->first();
    $purchase = $payment ? Purchase::find($payment->purchase_id) : null;

    // ===== 2) だめなら metadata / client_reference_id から復旧 =====
    $itemIdFromMeta     = $session->metadata->item_id ?? null;
    $purchaseIdFromMeta = $session->metadata->purchase_id ?? null;
    $clientRefId        = $session->client_reference_id ?? null;

    if ((!$payment || !$purchase) && ($purchaseIdFromMeta || $clientRefId)) {
        $pid = (int)($purchaseIdFromMeta ?: $clientRefId);
        if ($pid > 0) {
            $purchase = Purchase::find($pid);
            if ($purchase && !$payment) {
                $payment = Payment::where('purchase_id', $purchase->id)->first();
            }
        }
    }

    // ===== 3) さらにだめなら PaymentIntent.metadata で復旧（checkoutで設定していれば使える） =====
    if (!$payment || !$purchase) {
        $piId = $session->payment_intent ?? null;
        if ($piId) {
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $pi = $stripe->paymentIntents->retrieve($piId, []);
                $paymentIdFromPi  = (int)($pi->metadata->payment_id ?? 0);
                $purchaseIdFromPi = (int)($pi->metadata->purchase_id ?? 0);

                if ($paymentIdFromPi) {
                    $payment  = Payment::find($paymentIdFromPi) ?: $payment;
                }
                if ($purchaseIdFromPi) {
                    $purchase = Purchase::find($purchaseIdFromPi) ?: $purchase;
                }
                \Log::info('recovered by pi metadata', [
                    'pi' => $piId,
                    'payment_id' => $paymentIdFromPi,
                    'purchase_id' => $purchaseIdFromPi,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('pi retrieve failed', ['err' => $e->getMessage(), 'pi' => $piId]);
            }
        }
    }

    if (!$payment || !$purchase) {
        \Log::warning('no local records for session', [
            'session_id' => $sessionId,
            'meta_item' => $itemIdFromMeta,
            'meta_purchase' => $purchaseIdFromMeta,
            'client_ref' => $clientRefId,
        ]);
        return response('OK', 200);
    }

    // すでに確定済みなら何もしない（冪等）
    if ($purchase->status === 'succeeded') {
        return response('OK', 200);
    }

    if ($paid) {
        $piId = $session->payment_intent ?? $payment->payment_intent_id;

        DB::transaction(function () use ($purchase, $payment, $piId) {
            // アイテムをロックして二重売り防止
            $item = Item::lockForUpdate()->find($purchase->item_id);
            if (!$item) {
                \Log::warning('item not found at finalize', ['item_id' => $purchase->item_id]);
                return;
            }
            if ($item->is_sold) {
                \Log::info('item already sold at finalize', ['item_id' => $item->id]);
                return;
            }

            // 支払い・購入の確定
            $payment->update([
                'payment_intent_id' => $piId,
                'status'            => 'succeeded',
            ]);
            $purchase->update(['status' => 'succeeded']);

            \Log::info('updating sold', ['item_id' => $item->id, 'purchase_id' => $purchase->id]);

            $affected = Item::whereKey($item->id)
                ->where('is_sold', false)
                ->update(['is_sold' => true, 'sold_at' => now()]);

            \Log::info('marked sold (checkout.completed)', [
                'item_id'  => $item->id,
                'affected' => $affected,
            ]);
        });
    } else {
        $payment->update(['status' => 'awaiting_payment']);
        $purchase->update(['status' => 'awaiting_payment']);
    }

    \Log::info('sold updated (exit)', [
        'session_id'  => $sessionId,
        'purchase_id' => $purchase->id ?? null,
        'payment_id'  => $payment->id ?? null,
    ]);

    return response('OK', 200);
    }
}