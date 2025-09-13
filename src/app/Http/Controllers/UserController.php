<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{



    public function show()
    {
        return view('profile', ['user'=>Auth::user()]);
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->fill($request->only(['name', 'postal_code', 'address', 'building']));

        if($request->hasFile('profile_img')) {
            $path = $request->file('profile_img')->store('profile_imgs', 'public');
            $user->profile_img = $path;
        }

        $user->is_profile_set = true;
        $user->save();

        return redirect('/mypage');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        $sellingItems = $user->items ?? collect();
        $purchasedItems = $user->purchases()->with('item')->get()->pluck('item');

        return view('mypage', compact('user', 'page', 'sellingItems', 'purchasedItems'));
    }
}
