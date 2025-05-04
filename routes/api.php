<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payments\KCBMpesaExpressController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/payment/callback', [KCBMpesaExpressController::class, 'handleCallback'])->name('api.payment.callback');
