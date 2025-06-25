<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
/*         $user = Auth::user();
        // 通常のログイン後の遷移先
        return redirect()->intended('/mypage');
 */    }
}


CustomRegisterResponse.php

AppServiceProvider.php（バインドしているか）

ProfileController（edit() と update()）

routes/web.php にある /mypage/profile のルート定義

resources/views/mypage/profile.blade.php（可能なら）

