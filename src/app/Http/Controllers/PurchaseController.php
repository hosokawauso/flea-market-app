<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    /* public Item $item;
    public $user; */


/*     public $payment_method = '';
 */


/*     public $purchase = [
        'postal_code' => '',
        'address' => '',
        'building' => '',
    ];
 */
/*     public bool $changeAddress = false;
 */

    /** 購入確認画面を表示 */
    public function confirm(Request $request, Item $item)
    {

        $user = Auth::user();
        $payments = Payment::all();

        $purchase = session('purchase_address', [
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        $changeAddress = false;

        if ($request->has('cancel')) {
            session()->flash('error', '購入に失敗しました。もう一度お試しください。');
        }

        return view('purchase', compact('item', 'user', 'payments', 'purchase', 'changeAddress'));

    }

    public function edit(Item $item)
    {

        return view('purchase-address', compact('item'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {
        /* dd('リクエスト到達', $request->all()); */

        $address = $request->only(['postal_code', 'address', 'building']);

        session([
            'purchase_address' => $address
        ]);

        /* dd(session('purchase_address')); */

        return redirect()->route('purchase.confirm', ['item'=>$item->id]);

    }

    public function toggleAddress(): void
    {
        $this->changeAddress = !$this->changeAddress;
    }

    /* public function store(PurchaseRequest $request, Item $item)
    {
        /* $validated = $request->validated(); */

       /*  $payment = Payment::find($request->payment_method); */

        /* dd($request->payment_method);

        $purchase = session('purchase_address', [
            'postal_code' => Auth::user()->postal_code,
            'address' => Auth::user()->address,
            'building' => Auth::user()->building,
        ]);

        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'payment_id' => $paymentMethod,
            'purchase_postal_code' => $purchase['postal_code'],
            'purchase_address' => $purchase['address'],
            'purchase_building' => $purchase['building'],
        ]);

        $item->is_sold = true;
        $item->save();

        session()->forget('purchase_address');

        return redirect('/mypage?page=buy');
    } */

}
