<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // プロフィール未設定ならプロフィール編集画面へリダイレクト
        if (!$user->is_profile_set) {
            return redirect('/mypage/profile');
        }

        // 通常のログイン後の遷移先
        return redirect()->intended('/mypage');
    }
}
