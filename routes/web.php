<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// ブログ一覧・詳細表示
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');

// お問い合わせフォーム
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'sendMail']);
Route::get('/contact/complete', [ContactController::class, 'complete'])->name('contact.complete');

// ねこちゃん一覧
Route::get('/cats', [\App\Http\Controllers\CatController::class, 'index'])->name('cats.index');

// 管理者用ルート
Route::prefix('admin')->name('admin.')->middleware('admin.ip')->group(function () {
    // 未ログイン時のみアクセス可能なルート
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    // ログイン時のみアクセス可能なルート
    Route::middleware('auth')->group(function () {
        // ダッシュボード（全ユーザー）
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // ブログ管理（staffとmanagerのみ、adminは除外）
        Route::middleware('role:staff')->group(function () {
            Route::resource('blogs', AdminBlogController::class)->except('show');
        });

        // ユーザー管理（adminのみ）
        Route::middleware('role:admin')->group(function () {
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
        });

        // ログアウト（全ユーザー）
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
