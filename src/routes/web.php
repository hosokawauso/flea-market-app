<?php

//use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Route::get('/', [ItemController::class,'index']);
Route::get('/mypage/profile', [UserController::class, 'profile'])->middleware('auth');
Route::get('/mypage', [UserController::class, 'mypage'])->middleware('auth');