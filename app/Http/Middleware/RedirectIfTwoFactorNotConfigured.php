<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfTwoFactorNotConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // ログイン済みで、かつ2FAが設定されていない場合
        if ($user && (!$user->two_factor_secret || !$user->two_factor_confirmed_at)) {
            // 2FA設定画面へのルート名が 'admin.two-factor-auth.show' であることを確認
            // 既に2FA設定画面にいる場合は無限ループを防ぐ
            if ($request->routeIs('admin.two-factor-auth.show')) {
                return $next($request);
            }
            return redirect()->route('admin.two-factor-auth.show');
        }

        return $next($request);
    }
}