<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public test route
Route::get("/test", function () {
    return response()->json(["message" => "API is working"]);
});

// Auth routes (public)
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login",    [AuthController::class, "login"]);

// Protected routes (any authenticated user)
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/user",    [AuthController::class, "user"]);
});

// Admin only routes
Route::middleware(["auth:sanctum", "role:admin"])->group(function () {
    Route::get("/users",         [UserController::class, "index"]);
    Route::delete("/users/{id}", [UserController::class, "destroy"]);
});
