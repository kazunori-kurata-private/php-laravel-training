<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->redirectGuestsTo(fn() => route('admin.login'))
            ->redirectUsersTo(fn() => route('admin.blogs.index'))
            ->alias([
                'admin.ip' => \App\Http\Middleware\AdminIpRestriction::class,
                'role' => \App\Http\Middleware\CheckRole::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                Log::error('権限エラーが発生しました', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'ip_address' => request()->ip(),
                    'message' => $e->getMessage(),
                ]);
            }
        });
    })->create();
