<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('labels', LabelController::class);
    Route::apiResource('posts', PostController::class);

    Route::get('/comments', [CommentController::class, 'index'])->name('comments');
    Route::get('/post/{post}/comments', [CommentController::class, 'post_comments'])->name('post.comments');
    Route::post('/post/{post}/comment', [CommentController::class, 'store'])->name('post.store.comment');
});

