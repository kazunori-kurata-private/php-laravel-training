<?php

use App\Http\Controllers\Admin\TwoFactorAuthController;
use App\Http\Controllers\Admin\TwoFactorChallengeController;
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
        
        // 2FAチャレンジ（未ログイン状態でもアクセス可能）
        Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'show'])
            ->name('two-factor.challenge');
        Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'verify'])
            ->name('two-factor.verify');
        Route::post('/two-factor-recovery', [TwoFactorChallengeController::class, 'verifyRecoveryCode'])
            ->name('two-factor.recovery');
    });

    // ログイン時のみアクセス可能なルート
    Route::middleware(['auth'])->group(function () {
        // 2FA関連のルート (2fa.not.configured ミドルウェアの対象外)
        Route::get('/two-factor-auth', [TwoFactorAuthController::class, 'showTwoFactorAuthForm'])
            ->name('two-factor-auth.show');
        Route::post('/two-factor-auth', [TwoFactorAuthController::class, 'enableTwoFactorAuth'])
            ->name('two-factor-auth.enable');
        Route::delete('/two-factor-auth', [TwoFactorAuthController::class, 'disableTwoFactorAuth'])
            ->name('two-factor-auth.disable');

        // 2FA設定が未完了の場合のリダイレクトミドルウェアを適用するグループ
        Route::middleware('2fa.not.configured')->group(function () {
            // ダッシュボード（全ユーザー）
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            
            // 既存の2faミドルウェアが必要なルート（2FAチャレンジ画面へのリダイレクト）
            Route::middleware('2fa')->group(function () {
                // ブログ管理（staffとmanagerのみ、adminは除外）
                Route::middleware('role:staff')->group(function () {
                    Route::resource('blogs', AdminBlogController::class)->except('show');
                });

                // ユーザー管理（adminのみ）
                Route::middleware('role:admin')->group(function () {
                    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
                    Route::post('/users', [UserController::class, 'store'])->name('users.store');
                });
            });
        });

        // ログアウト（全ユーザー）
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
