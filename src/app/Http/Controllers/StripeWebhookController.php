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

        if ($type === 'checkout.session.completed') {
            $session = $event->data->object;
            $sessionId = $session->id ?? null;

            \Log::info('branch: checkout.completed', [
                'session_id'     => $sessionId,
                'payment_status' => $session->payment_status ?? null,
            ]);

    try {
        $purchase = Purchase::where('checkout_session_id', $sessionId)->first();
        $payment  = Payment::where('checkout_session_id', $sessionId)->first();

        if (!$purchase || !$payment) {
            \Log::warning('no local records for session', ['session_id' => $sessionId]);
            return response('OK', 200);
        }

        if ($purchase->status === 'succeeded') {
            \Log::info('already succeeded', ['purchase_id' => $purchase->id]);
            return response('OK', 200);
        }

        if (($session->payment_status ?? null) === 'paid') {
            $piId = $session->payment_intent ?? null;

            DB::transaction(function() use ($purchase, $payment, $piId) {
                $item = Item::lockForUpdate()->findOrFail($purchase->item_id);
                if ($item->is_sold) {
                    \Log::info('already sold at finalize', ['item_id' => $item->id]);
                    return;
                }

                $payment->update([
                    'payment_intent_id' => $piId,
                    'status' => 'succeeded',
                ]);

                $purchase->update(['status' => 'succeeded']);

                $affected = Item::whereKey($item->id)
                    ->where('is_sold', false)
                    ->update(['is_sold' => true, 'sold_at' =>now()]);
                \Log::info('marked sold (checkout.completed)', [
                    'item_id' => $item->id, 'affected' => $affected,
                ]);
            });
        } else {
            $payment->update(['status' => 'awaiting_payment']);
            $purchase->update(['status' => 'awaiting_payment']);
            \Log::info('awaiting payment set', ['purchase_id' => $purchase->id]);
        }

        return response('OK', 200);
        } catch (\Throwable $exception) {
            \Log::error('checkout.completed handler error', [
                'session_id' => $sessionId,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
            throw $exception;
        }
    }

        if ($type === 'payment_intent.succeeded') {
            $pi = $event->data->object;

            $payment = Payment::where('payment_intent_id', $pi->id)->first();

            if (!$payment) {
                $purchaseId = (int)($pi->metadata->purchase_id ?? 0);
                $payment = Payment::where('id', (int)($pi->metadata->payment_id ?? 0))->first();
                if ($payment && empty($payment->payment_intent_id)) {
                    $payment->payment_intent_id = $pi->id;
                    $payment->save();
                }
            }
            if (!$payment) return response('No payment match', 200);

            $purchase = Purchase::find($payment->payment_id ? $payment->payment_id : ($pi->metadata->purchase_id ?? 0))->first();
            $purchase = Purchase::where('id', (int)($pi->metadata->purchase_id ?? 0))->first();

            if (!$purchase) return response ('No purchase match', 200);
            if ($purchase->status === 'succeeded') return response('OK', 200);

            DB::transaction(function () use ($purchase, $payment, $pi) {
                $item = Item::lockForUpdate()->findOrFail($purchase->item_id);
                if ($item->is_sold) return;

                $payment->update([
                    'payment_intent_id' => $pi->id,
                    'amount' => (int)$pi->amount_received,
                    'currency' => (string)$pi->currency,
                    'status' => 'succeeded',
                ]);

                $purchase->update(['status' => 'succeeded']);

                $item->is_sold = true;
                $item->sold_at = now();
                $item->save();
            });

            return response('OK', 200);
        }

        if ($type === 'payment_intent.payment_failed') {
            $pi = $event->data->object;
            $payment = Payment::where('payment_intent_id', $pi->id)->first();
            if ($payment) {
                $payment->update(['status'=> 'failed']);
                PUrchase::where('payment_id', $payment->id)->update(['status' => 'failed']);
            }
            return response('OK', 200);
        }

        if ($type === 'checkout.session.expired') {
            $session = $event->data->object;
            Purchase::where('checkout_session_id', $session->id)
                ->whereIn('status', ['pending', 'awaiting_payment'])
                ->update(['status' => 'canceled']);
            Payment::where('checkout_session_id', $session->id)
                ->whereIn('status', ['pending', 'awaiting_payment'])
                ->update(['status' => 'cancelId']);
            return response('OK', 200);
        }

        return response('OK', 200);
    }

}