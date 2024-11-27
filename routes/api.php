<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\PackageApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\WebsiteApiController;
use App\Http\Controllers\Api\AuthController;

Route::post('login', [AuthController::class, 'login']);
// Users
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [UserApiController::class, 'index']); // Endpoint untuk semua user
    Route::get('users/{id}', [UserApiController::class, 'show']); // Endpoint untuk satu user
    Route::put('/users/{id}', [UserApiController::class, 'update']); // Endpoint untuk update user berdasarlam role
    Route::post('/users', [UserApiController::class, 'store']); // Endpoint untuk create user khusus user admin
    Route::delete('/users/{id}', [UserApiController::class, 'destroy']); //Endpoint Super Admin untuk menghapus user
});

// Packages
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('packages', [PackageApiController::class, 'getPackages']); // Endpoint untuk View Packages
});

// Subscriptions
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('subscriptions', [SubscriptionApiController::class, 'getSubscriptions']); // Endpoint
});

// Payments
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('payments', [PaymentApiController::class, 'getPayments']); // Endpoint untuk payment
});

// Websites
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('websites', [WebsiteApiController::class, 'getWebsites']); //
});
