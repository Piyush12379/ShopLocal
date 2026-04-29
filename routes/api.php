<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\AdminApiController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES — no authentication needed
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login',    [AuthApiController::class, 'login']);

// Products — anyone can browse
Route::get('/products',           [ProductApiController::class, 'index']);
Route::get('/products/{product}', [ProductApiController::class, 'show']);
Route::get('/categories',         [ProductApiController::class, 'categories']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES — need Bearer token
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // Customer — orders
    Route::get('/orders',         [OrderApiController::class, 'index']);
    Route::post('/orders',        [OrderApiController::class, 'store']);
    Route::get('/orders/{order}', [OrderApiController::class, 'show']);

    // Vendor — product management
    Route::prefix('vendor')->group(function () {
        Route::post('/products',          [ProductApiController::class, 'store']);
        Route::put('/products/{product}', [ProductApiController::class, 'update']);
        Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);
    });

    // Admin only
    Route::prefix('admin')->group(function () {
        Route::get('/stats',                        [AdminApiController::class, 'stats']);
        Route::get('/vendors/pending',              [AdminApiController::class, 'pendingVendors']);
        Route::post('/vendors/{user}/approve',      [AdminApiController::class, 'approveVendor']);
        Route::get('/orders',                       [AdminApiController::class, 'orders']);
    });

});