<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Livewire\PurchasePage;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

//Route::get('/', [ItemController::class,'index']);
Route::get('/mypage/profile', [UserController::class, 'edit'])->middleware(['auth', 'verified']);
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



/*メール認証用ルート*/
Route::get('/email/verify', function() {
  return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();

  return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function(Request $request) {
  $request->user()->SendEmailVerificationNotification();

  return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/profile', function () {
})->middleware('verified');