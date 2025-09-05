<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\CheckoutRequest;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe as StripeSDK;

class PaymentController extends Controller
{
    public function checkout(CheckoutRequest $request, Item $item)
    {

    $user = Auth::user();
    $validated = $request->validated();
    $paymentMethod = $validated['payment_method'];
    $total = (int) $item->price;

     // テスト環境 or 明示的フェイク時はStripeを呼ばずに完結
    if(app()->environment('testing') || config('payments.fake')) {

        $addr = session('purchase_address', []);


        DB::transaction(function () use ($validated, $addr, $item) {
            $purchase =Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'purchase_postal_code' => $addr['postal_code'] ?? null,
                'purchase_address' => $addr['address'] ?? null,
                'purchase_building' => $addr['building'] ?? null,
            ]);

            $payment = Payment::create([
                'purchase_id' => $purchase->id,
                'amount' => $item->price ?? 0,
                'currency' => 'jpy',
                'method' =>  $validated['payment_method'],
                'status' => 'pending',
            ]);

            $purchase->update(['payment_id' => $payment->id]);

        });

        return response()->noContent(200);
    }

    //コンビの払いは１２０円以上
    if ($paymentMethod === 'konbini' && $total < 120) {
        return back()
            ->withErrors(['payment_method' => 'コンビニ払いは合計120円以上でご利用いただけます'])
            ->withInput();
    }

    $allowed = [
        'card' => ['card'],
        'konbini' => ['konbini'],
    ];

    if(!isset($allowed[$paymentMethod])) {
        return back()
            ->withErrors(['payment_method' => '支払い方法を選択してください'])
            ->withInput();
        }

    $purchaseAddress = session('purchase_address', [
        'postal_code' => $user->postal_code,
        'address' => $user->address,
        'building' => $user->building,
    ]);

    if (empty($purchaseAddress['postal_code']) || empty($purchaseAddress['address'])) {
        return back()
            ->withErrors([
                'purchase_address' => "配送先住所が入力されていません。\n「変更する」から住所を入力してください。"
            ]);
    }

    $metadata = [
        'user_id' =>(string) $user->id,
        'item_id' => (string)$item->id,
        'payment_method' => $paymentMethod,
        'postal_code' => (string) ($purchaseAddress['postal_code'] ?? ''),
        'address'     => Str::limit((string) ($purchaseAddress['address'] ?? ''), 500, ''),
        'building'    => Str::limit((string) ($purchaseAddress['building'] ?? ''), 500, ''),
    ];

    stripeSDK::setApiKey(config('services.stripe.secret'));

    DB::beginTransaction();
    try {
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            /* 'payment_id' => $payment->id, */
            'status' => 'pending',
            'purchase_postal_code' => (string) ($purchaseAddress['postal_code'] ?? ''),
            'purchase_address'     => (string) ($purchaseAddress['address'] ?? ''),
            'purchase_building'    => (string) ($purchaseAddress['building'] ?? ''),

        ]);

        $payment = $purchase->payments()->create([
            /* 'user_id' => $user->id,
            'item_id' => $item->id, */
            /* 'purchase_id' => $purchase->id, */
            'amount' => (int)$item->price,
            'currency' => 'jpy',
            'method' => $paymentMethod,
            'status' => 'pending',
            'expires_at' => $paymentMethod === 'konbini' ? now()->addDays(3) : null,
        ]);

        DB::commit();
    } catch (\Throwable $exception) {
        DB::rollBack();
        return back()->withErrors(['payment' => '決済の開始に失敗しました: '.$exception->getMessage()])->withInput();
    }

    try{
        $params = [
            'mode' => 'payment',
            'payment_method_types' => $allowed[$paymentMethod],
            'line_items' => [[
                'price_data' => [
                    'currency' =>'jpy',
                    'unit_amount' => (int)$item->price,
                    'item_data' => ['name' => $item->item_name],
                ],
                'quantity' => 1,
            ]],

            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel', ['purchase' => $purchase->id]),
            'metadata' => array_merge($metadata, [
                'purchase_id' => (string)$purchase->id,
                'payment_id' => (string)$payment->id,
            ]),
            'payment_intent_data' => [
                'metadata' => array_merge($metadata, [
                    'purchase_id' => (string)$purchase->id,
                    'payment_id' => (string)$payment->id,
                ]),
            ],
            'locale' => 'ja',
            'customer_email' => $user->email,
        ];
        if ($paymentMethod === 'konbini') {
            $params['payment_method_options'] = [
                'konbini' => ['expires_after_days' => 3],      //支払期限が3日以内
            ];
        }

        $session = StripeCheckoutSession::create($params);

        $payment->update(['checkout_session_id' => $session->id]);

        logger()->info('payment attrs', [
            'attrs' => [
                'purchase_id' => $purchase->id,
                'payment_id'          => $payment->id,
                'checkout_session_id' => $session->id,
                'amount'  => (int)$item->price,
                'currency'=> 'jpy',
                'method'  => $paymentMethod,
                'status'  => 'pending',
            ]
        ]);

        return redirect()->away($session->url);

    } catch (\Throwable $exception) {
        $payment->update(['status' => 'canceled']);
        $purchase->update(['status' => 'canceled']);
        return back()->withErrors(['payment' => '決済の開始に失敗しました:' . $exception->getMessage()])->withInput();
        }
    }

    public function success(Request $request)
    {

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect('/')->withErrors(['payment' => 'セッションIDが見つかりません']);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            return redirect('/mypage?page=buy')->with('success', '購入が確定しました');
        }

        return view('payment.pending', [
            'expires_at' => null,
            'session_id' => $sessionId,
        ]);

    }
}