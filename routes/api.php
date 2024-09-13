<?php

use App\Http\Controllers\User\InviteController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/invite-user', [UserController::class, 'AddUser']);
});

//API for testing
Route::post('/test-invite-user', [UserController::class, 'AddUser']);

