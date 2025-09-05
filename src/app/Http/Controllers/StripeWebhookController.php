<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook as StripeWebhook;
use Stripe\StripeClient;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Item;

class StripeWebhookController extends CashierController
{

    public function handle(Request $request)
    {
        $signingSecret = config('services.stripe.webhook.secret');
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = StripeWebhook::constructEvent($payload, $signature, $signingSecret);
        } catch (\Throwable $exception) {
            return response('Invalid signature', 400);
        }

        $type = $event->type;
        Log::info('stripe webhook received', ['type' => $type]);

        if ($type === 'checkout.session.completed') {
    $session   = $event->data->object;
    $sessionId = $session->id ?? null;

    \Log::info('branch: checkout.completed', [
        'session_id'     => $sessionId,
        'payment_status' => $session->payment_status ?? null,
    ]);

    try {
        // 1) まずは checkout_session_id で紐づく Payment を探す
        $payment  = Payment::where('checkout_session_id', $sessionId)->first();
        $purchase = $payment ? Purchase::find($payment->purchase_id) : null;

        // 2) 見つからなければ、PaymentIntent を取得して metadata から復旧
        if (!$payment || !$purchase) {
            $piId = $session->payment_intent ?? null;
            if ($piId) {
                try {
                    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                    $pi = $stripe->paymentIntents->retrieve($piId, []);
                    $recoveredPaymentId  = (int)($pi->metadata->payment_id ?? 0);
                    if ($recoveredPaymentId) {
                        $payment  = Payment::find($recoveredPaymentId);
                        $purchase = $payment ? \App\Models\Purchase::find($payment->purchase_id) : null;
                        \Log::info('recovered by pi metadata', [
                            'pi'          => $piId,
                            'payment_id'  => $recoveredPaymentId,
                            'purchase_id' => (int)($pi->metadata->purchase_id ?? 0),
                        ]);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('pi retrieve failed', ['err'=>$e->getMessage(), 'pi'=>$piId]);
                }
            }
        }

        if (!$payment || !$purchase) {
            \Log::warning('no local records for session', ['session_id' => $sessionId]);
            return response('OK', 200);
        }
        if ($purchase->status === 'succeeded') {
            return response('OK', 200);
        }

        $paid = ($session->payment_status ?? null) === 'paid';
        if ($paid) {
            $piId = $session->payment_intent ?? null;

            DB::transaction(function() use ($purchase, $payment, $piId) {
                $item = Item::lockForUpdate()->find($purchase->item_id);
                if (!$item) {
                    \Log::warning('item not found at finalize', ['item_id'=>$purchase->item_id]);
                    return;
                }
                if ($item->is_sold) return;

                $payment->update([
                    'payment_intent_id' => $piId ?: $payment->payment_intent_id,
                    'status'            => 'succeeded',
                ]);
                $purchase->update(['status' => 'succeeded']);

                $affected = Item::whereKey($item->id)
                    ->where('is_sold', false)
                    ->update(['is_sold' => true, 'sold_at' => now()]);
                \Log::info('marked sold (checkout.completed)', ['item_id' => $item->id, 'affected' => $affected]);
            });
        } else {
            $payment->update(['status' => 'awaiting_payment']);
            $purchase->update(['status' => 'awaiting_payment']);
        }

        return response('OK', 200);
    } catch (\Throwable $e) {
        \Log::error('checkout.completed handler error', [
            'session_id' => $sessionId,
            'error'      => $e->getMessage(),
            'file'       => $e->getFile(),
            'line'       => $e->getLine(),
        ]);
        return response('OK', 200);
    }
}


    }
}