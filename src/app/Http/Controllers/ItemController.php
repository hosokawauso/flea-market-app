<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'recommend');

        $recommendItems = collect();
        $favoriteItems = collect();

        if($page === 'recommend') {
            $query = Item::query();

            if(Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            $recommendItems = $query->inRandomOrder()->limit(8)->get();

        }

        if($page === 'mylist') {
            if(Auth::check()) {
                $favoriteItems = Auth::user()
                        ->favorites()
                        ->with('item')
                        ->get()
                        ->pluck('item')
                        ->filter();
            }
        }

        return view('index', compact('page', 'recommendItems', 'favoriteItems'));
        
    }

/*     public function search(Request $request)
    {
    $keyword = $request->input('keyword');
    $items = Item::where('name', 'like', "%{$keyword}%")->get();

    return view('search', compact('items'));
    }
 */

}
