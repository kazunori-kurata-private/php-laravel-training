<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorChallengeController extends Controller
{
    protected $twoFactorAuthService;

    public function __construct(TwoFactorAuthService $twoFactorAuthService)
    {
        $this->twoFactorAuthService = $twoFactorAuthService;
    }

    /**
     * 2FAチャレンジ画面を表示
     */
    public function show()
    {
        // セッションにログインIDがない場合はログイン画面にリダイレクト
        if (!session()->has('login.id') || !session()->has('2fa_required')) {
            return redirect()->route('admin.login');
        }

        if (RateLimiter::tooManyAttempts('2fa-challenge', 10)) {
            return back()->withErrors(['code' => '試行回数が多すぎます。']);
        }

        return view('admin.two-factor-challenge');
    }

    /**
     * 2FAコードを検証
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // レート制限チェック
        if (RateLimiter::tooManyAttempts('two-factor', 5)) {
            return back()->withErrors([
                'code' => '試行回数が多すぎます。しばらく待ってから再試行してください。'
            ]);
        }

        $userId = session()->get('login.id');
        
        // ユーザー情報をキャッシュから取得（パフォーマンス向上）
        $user = cache()->remember("user.{$userId}", 300, function() use ($userId) {
            return Auth::getProvider()->retrieveById($userId);
        });

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // 2FAコードを検証
        if (isset($user->two_factor_secret) && $user->two_factor_secret && $this->twoFactorAuthService->verifyCode($user->two_factor_secret, $request->code)) {
            // レート制限をクリア
            RateLimiter::clear('two-factor');
            
            // 2FA検証成功のセッションを設定
            $request->session()->put('2fa_verified', true);
            $request->session()->forget(['login.id', '2fa_required']);
            
            // セッションを再生成
            $request->session()->regenerate();
            
            // ログイン状態を設定（重複ログインを防ぐ）
            $response = redirect()->intended('/admin');
            
            // ログイン状態を設定（重複ログインを防ぐ）
            if (!Auth::check()) {
                Auth::login($user);
            }
            
            return $response;
        }

        // レート制限を増加
        RateLimiter::hit('two-factor');

        return back()->withErrors([
            'code' => '無効な認証コードです。'
        ]);
    }

    /**
     * リカバリーコードで検証
     */
    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $userId = session()->get('login.id');
        
        // ユーザー情報をキャッシュから取得（パフォーマンス向上）
        $user = cache()->remember("user.{$userId}", 300, function() use ($userId) {
            return Auth::getProvider()->retrieveById($userId);
        });

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // リカバリーコードを検証
        if ($this->twoFactorAuthService->verifyRecoveryCode($user, $request->recovery_code)) {
            // 2FA検証成功のセッションを設定
            $request->session()->put('2fa_verified', true);
            $request->session()->forget(['login.id', '2fa_required']);
            
            // セッションを再生成
            $request->session()->regenerate();
            
            // ログイン状態を設定
            $response = redirect()->intended('/admin');
            
            // ログイン状態を設定
            Auth::login($user);
            
            return $response;
        }

        return back()->withErrors([
            'recovery_code' => '無効なリカバリーコードです。'
        ]);
    }
}
