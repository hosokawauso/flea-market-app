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

    public function update(AddressRequest $request)
    {
        $user = Auth::user();
        return view('purchase-address', compact('user', 'address'));
    }

    /** 実際の購入処理（POST 用） */
    public function purchase(Request $request, Item $item)
    {
        // 決済ロジック → 購入完了ページへリダイレクトなど
    }
}
