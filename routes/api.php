<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AdminController;
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
Route::get("/posts/{postId}/comments", [CommentController::class, "index"]);

// Authenticated routes
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout",                    [AuthController::class, "logout"]);
    Route::get("/user",                       [AuthController::class, "user"]);
    Route::post("/posts",                     [PostController::class, "store"]);
    Route::put("/posts/{id}",                 [PostController::class, "update"]);
    Route::delete("/posts/{id}",              [PostController::class, "destroy"]);
    Route::post("/categories",                [CategoryController::class, "store"]);
    Route::post("/tags",                      [TagController::class, "store"]);
    Route::post("/posts/{postId}/comments",   [CommentController::class, "store"]);
    Route::delete("/comments/{id}",           [CommentController::class, "destroy"]);
});

// Admin only routes
Route::middleware(["auth:sanctum", "role:admin"])->group(function () {
    Route::get("/users",          [UserController::class, "index"]);
    Route::delete("/users/{id}",  [UserController::class, "destroy"]);
    
    // Admin dashboard
    Route::get("/admin/dashboard", [AdminController::class, "dashboard"]);
    
    // Admin manage posts
    Route::get("/admin/posts", [AdminController::class, "posts"]);
    Route::put("/admin/posts/{id}/approve", [AdminController::class, "approvePost"]);
    Route::put("/admin/posts/{id}/reject", [AdminController::class, "rejectPost"]);
    Route::delete("/admin/posts/{id}", [AdminController::class, "deletePost"]);
    
    // Admin manage users
    Route::get("/admin/users", [AdminController::class, "users"]);
    Route::get("/admin/users/{id}", [AdminController::class, "showUser"]);
    Route::put("/admin/users/{id}/role", [AdminController::class, "changeUserRole"]);
    Route::delete("/admin/users/{id}", [AdminController::class, "deleteUser"]);
    
    // Admin manage comments
    Route::get("/admin/comments", [AdminController::class, "comments"]);
    Route::delete("/admin/comments/{id}", [AdminController::class, "deleteComment"]);
});
