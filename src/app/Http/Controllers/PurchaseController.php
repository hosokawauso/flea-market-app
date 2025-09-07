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
        $address = $request->only(['postal_code', 'address', 'building']);

        session([
            'purchase_address' => $address
        ]);

        return redirect()->route('purchase.confirm', ['item'=>$item->id]);

    }

    public function toggleAddress(): void
    {
        $this->changeAddress = !$this->changeAddress;
    }

}
