<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

            $recommendItems = $query->inRandomOrder()->get();

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

    public function show(Item $item)
    {
        $comments = $item->comments()->with('user')->get();

        $item->load(['categories', 'comments.user']);

        return view('item', compact('item', 'comments'));
    }

    public function edit()
    {
        $categories = Category::all();

        return view('sell', compact('categories'));
    }

    public function sell(ExhibitionRequest $request)
    {
        $user = Auth::user();

        $path = $request->file('item_img')->store('item_imgs', 'public');

        $item = $user->items()->create([
            'condition'   => $request->input('condition'),
            'item_name'   => $request->input('item_name'),
            'brand_name'  => $request->input('brand_name'),
            'description' => $request->input('description'),
            'price'       => $request->input('price'),
            'item_img'    => $path,
            'is_sold'     => false,
        ]);

        $item->categories()->attach($request->input('category'));

        return redirect('/mypage?page=sell');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $recommendItems = Item::searchItemName($keyword)->get();

        $user = Auth::user();
        $favoriteItems = $user
            ? $user->favorites()->whereHas('item', function($query) use ($keyword) {
                $query->searchItemName($keyword);
            })->with('item')->get()
            : collect();

        return view('index', [
            'recommendItems' => $recommendItems,
            'favoriteItems' => $favoriteItems,
            'keyword' => $keyword,
            'page' => 'recommend',
        ]);
    }

}
