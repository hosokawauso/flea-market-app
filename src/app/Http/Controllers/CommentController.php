<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Comment;
use App\Models\Item;
use App\Http\Requests\CommentRequest;


class CommentController extends Controller
{
    public function store(Item $item, CommentRequest $request)
    {
        $user = Auth::user();

        $item->comments()->create([
            'body' => $request->input('body'),
            'user_id' => $user->id,
        ]);

        return back();
    }
}
