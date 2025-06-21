<?php

namespace App\Http\Controllers;

/* use App\Models\Item;
use App\Models\Payment;
 */use Illuminate\Http\Request;
/* use Stripe\Stripe;
use Stripe\PaymentIntent;
 */
class PaymentController extends Controller
{



/*     public function purchase(Request $request)
{
    $item = Item::findOrFail($request->item_id);

    // Stripe秘密キー設定
    Stripe::setApiKey(config('services.stripe.secret'));

    // 商品価格を元に決済用Intentを作成
    $paymentIntent = PaymentIntent::create([
        'amount' => $item->price, // ← 商品の価格をそのまま利用（円）
        'currency' => 'jpy',
        'payment_method_types' => ['card'],
    ]);

    // DBに保存（まだ支払い完了前でもOK）
    Payment::create([
        'user_id' => auth()->id(),
        'item_id' => $item->id,
        'stripe_payment_id' => $paymentIntent->id,
        'method' => 2, // カード支払い
        'amount' => $item->price, // ← 商品の金額を記録
    ]);

    return view('payments.confirm', [
        'clientSecret' => $paymentIntent->client_secret,
        'item' => $item,
    ]);
}
 */
}
