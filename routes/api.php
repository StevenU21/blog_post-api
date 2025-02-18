<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\LabelController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::apiResource('categories', CategoryController::class);

    Route::get('/labels/{label}/posts', [LabelController::class, 'label_posts'])->name('labels.post');
    Route::apiResource('labels', LabelController::class);

    Route::get('/posts/{user}/user', [PostController::class, 'user_posts'])->name('posts.user');
    Route::apiResource('posts', PostController::class);

    Route::prefix('/comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::get('/post/{post}', [CommentController::class, 'post_comments'])->name('post');
        Route::post('/post/{post}', [CommentController::class, 'store'])->name('post.store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });

    Route::apiResource('users', UserController::class)->only('index', 'show');

    Route::get('/users/profile/index', [ProfileController::class, 'profile'])->name('profile');

    Route::middleware('role:admin')->prefix('/admin')->name('admin.')->group(function () {
        // Role routes
        Route::get('/roles', [RoleController::class, 'index'])->name('index');
        Route::put('/roles/{user}/assign-role', [RoleController::class, 'assignRole'])->name('assign-role');
        // Permission routes
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{user}/list-permission', [PermissionController::class, 'getUserPermissions'])->name('permissions.list-permission');
        Route::post('/permissions/{user}/give-permission', [PermissionController::class, 'assignPermission'])->name('permissions.give-permission');
        Route::delete('/permissions/{user}/revoke-permission', [PermissionController::class, 'revokePermission'])->name('permissions.revoke-permission');
    });
});

