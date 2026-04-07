<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — AI Support Reply Assistant
|--------------------------------------------------------------------------
*/

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/profile', [AuthController::class, 'profile']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'stats']);

    // Messages — subscription limit checked before store
    Route::get('messages', [MessageController::class, 'index']);
    Route::get('messages/{id}', [MessageController::class, 'show']);
    Route::delete('messages/{id}', [MessageController::class, 'destroy']);

    Route::middleware('subscription.limit')->group(function () {
        Route::post('messages', [MessageController::class, 'store']);
    });
});
