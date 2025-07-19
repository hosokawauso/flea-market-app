<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Livewire\PurchasePage;
use Illuminate\Support\Facades\Route;

//Route::get('/', [ItemController::class,'index']);
Route::get('/mypage/profile', [UserController::class, 'edit'])->middleware('auth');
Route::post('/mypage/profile', [UserController::class, 'update'])->middleware('auth');
Route::get('/mypage', [UserController::class, 'mypage'])->middleware('auth');


Route::get('/', [ItemController::class, 'index']);
Route::get('/sell', [ItemController::class, 'edit'])->middleware('auth');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');
Route::get('/search', [ItemController::class, 'search']);
Route::post('/sell', [ItemController::class, 'sell'])->middleware('auth');;


Route::post('/item/{item}/favorites', [FavoriteController::class, 'toggle'])->middleware('auth')->name('item.favorite');

Route::post('/item/{item}/comments', [CommentController::class, 'store'])->middleware('auth')->name('item.comment.store');

Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])->middleware('auth')->name('purchase.confirm');
Route::get('/purchase/address/{item}', [PurchaseController::class, 'edit'])->middleware('auth')->name('purchase.address.edit');
Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
Route::post('/purchase/{item}', [PurchaseController::class, 'purchase'])->name('item.purchase');

