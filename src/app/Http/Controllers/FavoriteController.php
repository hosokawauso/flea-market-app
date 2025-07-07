<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
/*         if(auth()->check()) {
            return redirect('/login');
        }
 */
        $user = Auth::user();

        if($user->favoriteItems()->where('item_id', $item->id)->exists()) {
            $user->favoriteItems()->detach($item->id);
        } else {
            $user->favoriteItems()->attach($item->id);
        }

        return back();
    }
}