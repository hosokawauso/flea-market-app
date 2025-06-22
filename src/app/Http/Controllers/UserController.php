<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function show()
    {
        return view('mypage-profile');
    }

    public function store(RegisterRequest $request)
    {

    }

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
