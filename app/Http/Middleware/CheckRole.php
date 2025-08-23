<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param string $role 必要なロール（staff, manager, admin）
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // ユーザーがログインしていない場合
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        // 必要なロールに応じた権限チェック
        switch ($role) {
            case 'staff':
                if (!$user->isStaff() && !$user->isManager()) {
                    abort(403, 'このページを閲覧する権限がありません。管理者へ問い合わせてください。');
                }
                break;
            
            case 'manager':
                if (!$user->isManager() && !$user->isAdmin()) {
                    abort(403, 'このページを閲覧する権限がありません。管理者へ問い合わせてください。');
                }
                break;
            
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'このページを閲覧する権限がありません。管理者へ問い合わせてください。');
                }
                break;
            
            default:
                abort(403, '無効な権限指定です。');
        }

        return $next($request);
    }
}
