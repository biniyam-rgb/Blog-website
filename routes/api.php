<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes (require valid Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'user']);
});
