<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['create', 'edit']);
    Route::apiResource('products', ProductController::class)->except(['create', 'edit']);

    Route::prefix('reports')->group(function () {
        Route::get('analytics', [ReportController::class, 'analytics']);
    });

});
