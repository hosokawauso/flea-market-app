<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


Route::get('/mypage/profile', [UserController::class, 'show'])->middleware(['auth', 'verified'])->name('profile.show');
Route::post('/mypage/profile', [UserController::class, 'update'])->middleware('auth')->name('profile.updata');
Route::get('/mypage', [UserController::class, 'mypage'])->middleware('auth');


Route::get('/', [ItemController::class, 'index']);
Route::get('/sell', [ItemController::class, 'edit'])->middleware('auth');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');
Route::get('/search', [ItemController::class, 'search']);
Route::post('/sell', [ItemController::class, 'sell'])->middleware('auth');


//いいね
Route::post('/item/{item}/favorites', [FavoriteController::class, 'toggle'])->middleware('auth')->name('item.favorite');
//コメント
Route::post('/item/{item}/comments', [CommentController::class, 'store'])->middleware('auth')->name('item.comment.store');


Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])->middleware('auth')->name('purchase.confirm');
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');


Route::get('/purchase/address/{item}', [PurchaseController::class, 'edit'])->middleware('auth')->name('purchase.address.edit');
Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');


Route::post('/payment/checkout/{item}', [PaymentController::class, 'checkout'])->name('payment.checkout')->middleware('auth');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success')->middleware('auth');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel')->middleware('auth');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');


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