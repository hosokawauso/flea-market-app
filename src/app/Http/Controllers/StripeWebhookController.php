<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Webhook as StripeWebhook;

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

    // 非即時決済の確定：コンビニ等
    if ($type === 'payment_intent.succeeded'/*  || $type === 'checkout.session.async_payment_succeeded' */) {
        $pi = $event->data->object;
        $piId = $pi->id;

        $payment = Payment::where('payment_intent_id', $piId)->first();

        if (!$payment) {
            $paymentIdFromMeta = (int)($pi->metadata->payment_id ?? 0);
            if ($paymentIdFromMeta) {
                $payment = Payment::find($paymentIdFromMeta);
            }
        }

        if(!$payment) {
            Log::warning('pi.succeeded: payment not found', ['pi => $piId']);
            return response('OK', 200);
        }

        $purchase = Purchase::find($payment->purchase_id);
        if (!$purchase) {
            \Log::warning('pi.succeeded: purchase not found', ['payment_id' => $payment->id]);
            return response('OK', 200);
        }

        DB::transaction(function () use ($payment, $purchase) {
            $item = Item::lockForUpdate()->find($purchase->item_id);
            if (!$item || $item->is_sold) return;

            $payment->update(['status' => 'succeeded']);
            $purchase->update(['status' => 'succeeded']);
            $item->update(['is_sold' => true, 'sold_at' => now()]);
        });

        Log::info('sold updated via pi.succeeded', [
            'payment_id' => $payment->id,
            'purchase_id' => $purchase->id,
        ]);

        return response('OK', 200);
    }



    if ($type !== 'checkout.session.completed') {
        return response('OK', 200);
    }

    /** @var \Stripe\Checkout\Session $session */
    $session   = $event->data->object;
    $sessionId = $session->id ?? null;
    $paid      = ($session->payment_status ?? null) === 'paid';

    Log::info('branch: checkout.completed', [
        'session_id'     => $sessionId,
        'payment_status' => $session->payment_status ?? null,
    ]);

    // 1) まず checkout_session_id から Payment/Purchase を特定
    $payment  = Payment::where('checkout_session_id', $sessionId)->first();
    $purchase = $payment ? Purchase::find($payment->purchase_id) : null;

    // 2) だめなら metadata / client_reference_id から復旧
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

    // 3) さらに PaymentIntent.metadata で復旧（checkoutで設定していれば使える
    $piId = $session->payment_intent ?? null;
    if ($piId && $payment && !$payment->payment_intent_id) {
        $payment->update(['payment_intent_id' => $piId]);
    }

    if (!$payment || !$purchase) {
        Log::warning('no local records for session', [
            'session_id' => $sessionId,
            'meta_item' => $itemIdFromMeta,
            'meta_purchase' => $purchaseIdFromMeta,
            'client_ref' => $clientRefId,
        ]);
        return response('OK', 200);
    }

    // 冪等
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