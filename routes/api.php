<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\InviteController;
use App\Http\Controllers\User\PointsController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'AddUser']);
    Route::post('logout', [AuthController::class, 'AddUser']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/invite-user', [UserController::class, 'AddUser']);
});

//API for testing
Route::post('/test-invite-user', [UserController::class, 'AddUser']);
Route::post('/redeem-points', [PointsController::class, 'redeemPoints']);

Route::get('/admin/dashboard', [AdminController::class, 'showDashboard']);
