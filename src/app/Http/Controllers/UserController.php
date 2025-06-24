<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    
    
    
    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        $sellingItems = $user->items ?? collect();
        $purchasedItems = $user->purchase()->with('item')->get()->pluck('item');

        return view('mypage', compact('user', 'page', 'sellingItems', 'purchasedItems'));
    }

    /* public function store(RegisterRequest $request)
    {

    } */

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'postal_code' =>$request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
            'is_profile_set' => true,
        ]);

        return redirect ('/mypage')->with('message', 'プロフィールを更新しました');
    }
}
