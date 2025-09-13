<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $user = Auth::user();

        $item->comments()->create([
            'body' => $request->input('body'),
            'user_id' => $user->id,
        ]);

        return back();
    }
}
