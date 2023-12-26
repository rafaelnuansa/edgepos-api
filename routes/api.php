<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');
Route::get('/auth-check', [App\Http\Controllers\Api\LoginController::class, 'authCheck'])->name('auth.check');
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');

Route::middleware('auth:api')->group(function () {
    // Your other authenticated routes go here
    Route::get('/dashboard-data', [App\Http\Controllers\Api\DashboardController::class, 'getAllData']);
    Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'getProducts']);
    Route::get('/categories', [App\Http\Controllers\Api\ProductController::class, 'getCategories']);
    Route::get('/check-token', [App\Http\Controllers\Api\TokenValidationController::class, 'checkToken']);
    Route::get('/user', [App\Http\Controllers\Api\UserController::class, 'getUserByToken']);
});
