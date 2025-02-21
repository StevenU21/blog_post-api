<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
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
Route::post('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->middleware('auth:sanctum')->name('verification.resend');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/categories/{category}/posts', [CategoryController::class, 'category_posts'])->name('categories.post');
    Route::apiResource('categories', CategoryController::class);

    Route::get('/labels/{label}/posts', [LabelController::class, 'label_posts'])->name('labels.post');
    Route::apiResource('labels', LabelController::class);

    Route::get('/user/{user}/posts', [PostController::class, 'user_posts'])->name('posts.user');
    Route::get('/user/posts', [PostController::class, 'auth_user_posts'])->name('posts.auth.user');
    Route::apiResource('posts', PostController::class)->middlewareFor('show', 'track.views');

    Route::prefix('/comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::get('/post/{post}', [CommentController::class, 'post_comments'])->name('post');
        Route::post('/post/{post}', [CommentController::class, 'store'])->name('post.store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });

    Route::apiResource('users', UserController::class)->only('index', 'show');

    Route::prefix('/profile')->name('profile.')->group(function () {
        Route::get('/users/index', [ProfileController::class, 'profile'])->name('profile');
        Route::put('/update', [ProfileController::class, 'updateProfile']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

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

