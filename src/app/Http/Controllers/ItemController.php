<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;

class ItemController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
