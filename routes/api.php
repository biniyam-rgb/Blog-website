<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get("/test", function () {
    return response()->json(["message" => "API is working"]);
});

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login",    [AuthController::class, "login"]);
Route::get("/posts",      [PostController::class, "index"]);
Route::get("/posts/{id}", [PostController::class, "show"]);
Route::get("/categories", [CategoryController::class, "index"]);
Route::get("/tags",       [TagController::class, "index"]);

// Authenticated routes
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout",        [AuthController::class, "logout"]);
    Route::get("/user",           [AuthController::class, "user"]);
    Route::post("/posts",         [PostController::class, "store"]);
    Route::put("/posts/{id}",     [PostController::class, "update"]);
    Route::delete("/posts/{id}",  [PostController::class, "destroy"]);
    Route::post("/categories",    [CategoryController::class, "store"]);
    Route::post("/tags",          [TagController::class, "store"]);
});

// Admin only routes
Route::middleware(["auth:sanctum", "role:admin"])->group(function () {
    Route::get("/users",          [UserController::class, "index"]);
    Route::delete("/users/{id}",  [UserController::class, "destroy"]);
});
