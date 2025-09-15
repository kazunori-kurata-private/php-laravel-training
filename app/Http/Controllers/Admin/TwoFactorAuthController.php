<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorAuthController extends Controller
{
    protected $twoFactorAuthService;

    public function __construct(TwoFactorAuthService $twoFactorAuthService)
    {
        $this->twoFactorAuthService = $twoFactorAuthService;
    }

    public function showTwoFactorAuthForm(Request $request)
    {
        $user = Auth::user();
        
        // 既に2FAが有効な場合は設定済み画面を表示
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            return view('admin.two-factor-auth.enabled');
        }
        
        // セッションに秘密鍵を保存（再生成を防ぐ）
        if (!$request->session()->has('two_factor_secret')) {
            $qrData = $this->twoFactorAuthService->generateQrCode($user);
            $request->session()->put('two_factor_secret', $qrData['secret']);
            $request->session()->put('two_factor_qr_code', $qrData['qr_code']);
        }
        
        $qrCode = [
            'secret' => $request->session()->get('two_factor_secret'),
            'qr_code' => $request->session()->get('two_factor_qr_code')
        ];
        
        return view('admin.two-factor-auth.show', compact('qrCode'));
    }

    public function enableTwoFactorAuth(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = $request->session()->get('two_factor_secret');

        if (!$secret) {
            return redirect()->route('admin.two-factor-auth.show')
                ->withErrors(['code' => 'セッションが無効です。再度設定を開始してください。']);
        }

        if ($this->twoFactorAuthService->verifyCode($secret, $request->code)) {
            // リカバリーコードを生成
            $recoveryCodes = $this->twoFactorAuthService->generateRecoveryCodes();
            
            $user->update([
                'two_factor_secret' => $secret,
                'two_factor_confirmed_at' => Carbon::now(),
                'two_factor_recovery_codes' => json_encode($recoveryCodes),
            ]);

            // セッションから秘密鍵を削除
            $request->session()->forget(['two_factor_secret', 'two_factor_qr_code']);

            return redirect()->route('admin.two-factor-auth.show')
                ->with('status', '2FAが有効化されました。')
                ->with('recovery_codes', $recoveryCodes);
        }

        return back()->withErrors(['code' => '無効なコードです。']);
    }

    public function disableTwoFactorAuth(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return back()->with('status', '2FAが無効化されました。');
    }
}
