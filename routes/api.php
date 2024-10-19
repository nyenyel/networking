<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\BroadcastController\User;
use App\Http\Controllers\User\InviteController;
use App\Http\Controllers\User\PointsController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return  $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');;
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/invite-user', [UserController::class, 'AddUser']);
    Route::post('/test-invite-user', [UserController::class, 'AddUser']);
    Route::post('/redeem-points/{userStore}', [PointsController::class, 'redeemPoints']);
    Route::get('/transaction', [TransactionController::class, 'transaction']);
    Route::get('user/genealogy/{id}', [UserController::class, 'getGenealogy']);
    Route::get('/user/{id}/with-invites', [UserController::class, 'getUserWithInvites']);
    Route::post('/verify-email', [UserController::class, 'verifyEmail']);
    Route::post('/create-store', [UserController::class, 'createStore']);
    Route::post('/create-store-v2/{user}', [UserController::class, 'createStoreV2']);
});

//API for testing
// Route::post('/test-invite-user', [UserController::class, 'AddUser']);
// Route::post('/redeem-points', [PointsController::class, 'redeemPoints'])->middleware('auth:sanctum');
// Route::get('/transaction', [TransactionController::class, 'transaction'])->middleware('auth:sanctum');
// Route::get('user/genealogy/{id}', [UserController::class, 'getGenealogy']);
// Route::get('/user/{id}/with-invites', [UserController::class, 'getUserWithInvites']);
// Route::patch('/transaction-approve/{id}', [AdminController::class, 'approveRedeemRequest']);
// Route::patch('/transaction-reject/{id}', [AdminController::class, 'rejectRedeemRequest']);

Route::prefix('admin')->group(function () {
    Route::apiResource('user', UserController::class)->only('index')->middleware('auth:sanctum');
    Route::get('dashboard', [AdminController::class, 'showDashboard'])->middleware('auth:sanctum');
});

Route::post('/broadcasting/auth', [BroadcastController::class, 'authenticate']);
// Route::post('/broadcasting/auth', BroadcastController::class, 'authenticate');
