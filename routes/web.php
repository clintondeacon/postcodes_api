<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PostcodeController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['throttle:5,1'])->post('/stores', [StoreController::class, 'store']);
Route::middleware(['throttle:5,1'])->get('/stores/{storeId}', [StoreController::class, 'get']);

Route::middleware(['throttle:5,1'])->get('/postcodes/{postcodeId}', [PostcodeController::class, 'get']);
