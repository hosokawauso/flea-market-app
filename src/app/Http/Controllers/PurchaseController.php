<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    
    /** 購入確認画面を表示 */
    public function confirm(Item $item)
    {
        
        $user = Auth::user();
         
        return view('purchase', compact('item', 'user'));

    }

    public function edit(Item $item)
    {

        return view('purchase-address', compact('item'));
    }
    
     public function updateAddress(Request $request, Item $item)
    {
        /* dd('リクエスト到達', $request->all()); */

        $address = $request->only(['postal_code', 'address', 'building']);

        session([
            'purchase_address' => $address
        ]);

        return redirect()->route('purchase.confirm', ['item'=>$item->id]);

    }
}
