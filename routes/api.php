<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentReplyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;

// Auth Routes
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->middleware('auth:sanctum')->name('verification.resend');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Categories Routes
    Route::get('/categories/{category}/posts', [CategoryController::class, 'categoryPosts'])->middleware('cache.response');
    Route::apiResource('categories', CategoryController::class)->middlewareFor(['index', 'show'], 'cache.response');

    // Labels Routes
    Route::get('/tags/{tag}/posts', [TagController::class, 'tagPosts'])->name('tags.post')->middleware('cache.response');
    Route::apiResource('tags', TagController::class)->middlewareFor(['index', 'show'], 'cache.response');

    // Posts Routes
    Route::get('/user/{user}/posts', [PostController::class, 'userPosts'])->name('posts.user')->middleware('cache.response');
    Route::get('/user/posts', [PostController::class, 'authUserPosts'])->name('posts.auth.user')->middleware('cache.response');
    Route::get('/posts/search', [PostController::class, 'search']);
    Route::get('/posts/{post}', [PostController::class, 'show'])->middleware('track.views');
    Route::apiResource('posts', PostController::class)->middlewareFor('index', 'cache.response');

    // Comments Routes
    Route::prefix('/comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index')->middleware('cache.response');
        Route::get('/post/{post}', [CommentController::class, 'postComments'])->name('post')->middleware('cache.response');
        Route::post('/post/{post}', [CommentController::class, 'store'])->name('post.store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });

    // Replies Routes
    Route::prefix('/replies')->name('comments.')->group(function () {
        Route::get('/', [CommentReplyController::class, 'index'])->middleware('cache.response');
        Route::get('/comments/{comment}', [CommentReplyController::class, 'commentReplies'])->middleware('cache.response');
        Route::get('/{reply}/response', [CommentReplyController::class, 'replyResponses'])->middleware('cache.response');
        Route::post('/comment/{comment}/reply/{parent_reply?}', [CommentReplyController::class, 'store']);
        Route::put('/{reply}/update', [CommentReplyController::class, 'update']);
        Route::delete('/{reply}/destroy', [CommentReplyController::class, 'destroy']);
    });

    // Profile Routes
    Route::prefix('/profile')->name('profile.')->group(function () {
        Route::get('/users/index', [ProfileController::class, 'profile'])->name('profile');
        Route::put('/update', [ProfileController::class, 'updateProfile']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('/admin')->name('admin.')->group(function () {
        // Roles
        Route::get('/roles', RoleController::class);

        //Manage User
        Route::apiResource('users', UserController::class);

        //Dashboard
        Route::middleware('cache.response')->prefix('dashboard')->group(function () {
            Route::get('/totals', [DashboardController::class, 'getTotals']);
            Route::get('/recent-users', [DashboardController::class, 'getRecentUsers']);
            Route::get('/recent-posts', [DashboardController::class, 'getRecentPosts']);
            Route::get('/top-authors', [DashboardController::class, 'getTopAuthors']);
            Route::get('/top-categories', [DashboardController::class, 'getTopCategories']);
            Route::get('/top-posts', [DashboardController::class, 'getTopPosts']);
            Route::get('/new-users-date-range', [DashboardController::class, 'getNewUsersByDateRange']);
            Route::get('/new-users-by-filter', [DashboardController::class, 'getNewUsersByFilter']);
        });

        // Permissions
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('/{user}/list-permission', [PermissionController::class, 'getUserPermissions'])->name('permissions.listPermission');
            Route::post('/{user}/give-permission', [PermissionController::class, 'assignPermission'])->name('permissions.givePermission');
            Route::delete('/{user}/revoke-permission', [PermissionController::class, 'revokePermission'])->name('permissions.revokePermission');
        });
    });
});
