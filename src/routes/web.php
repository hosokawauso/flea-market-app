<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

//Route::get('/', [ItemController::class,'index']);
Route::get('/mypage/profile', [UserController::class, 'edit'])->middleware('auth');
Route::patch('/mypage/profile', [UserController::class, 'update'])->middleware('auth');
Route::get('/mypage', [UserController::class, 'mypage'])->middleware('auth');


Route::get('/', [ItemController::class, 'index']);
Route::get('/sell', [ItemController::class, 'edit']);
Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');
Route::post('/sell', [ItemController::class, 'sell']);

/* Route::get('/purchase/{item}', [PurchaseController::class, 'confirm']); */

Route::get('/item/{item}/favorites', [LikeController::class, 'favoriteItem'])->name('item.favorite');