<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class UserController extends Controller
{



    public function edit()
    {
        return view('profile', ['user'=>Auth::user()]);
    }

    public function update(AddressRequest $request)
    {
        /* $this->validate($request, (new ProfileRequest())->rules()); */

        $user = Auth::user();

        $user->fill($request->only(['profile_img', 'name', 'postal_code', 'address', 'building']));

/*         if($request->hasFile('profile_img')) {
            $path = $request->file('profile_img')->store('profile_imgs', 'public');
            $user->profile_img = $path;
        }
 */
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

    /* public function store(RegisterRequest $request)
    {

    } */

/*     public function update(ProfileRequest $request)
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
 */}
