<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/products', [OrderApiController::class, 'products']);
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::get('/orders/{order}', [OrderApiController::class, 'show']);
    Route::get('/kitchen', [OrderApiController::class, 'kitchen']);
});
