<?php

namespace App\Http\Controllers;

use App\Model\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function favoriteItem(Item $item)
    {
        if(auth()->check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if($user->favoriteItems()->where('item_id', $item->id)->exists()) {
            $user->favoriteItems()->detach($item->id);
        } else {
            $user->favoriteItems()->attach($item->id);
        }

        return back();
    }
}