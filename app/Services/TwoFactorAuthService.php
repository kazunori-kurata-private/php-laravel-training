<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateQrCode($user)
    {
      $secret = $this->google2fa->generateSecretKey();
      $qrCodeUrl = $this->google2fa->getQRCodeUrl(
        config('app.name'),
        $user->email,
        $secret
      );

      return [
        'secret' => $secret,
        'qr_code' => $this->generateQrCodeImage($qrCodeUrl),
      ];
    }

    protected function generateQrCodeImage($url)
    {
      $renderer = new ImageRenderer(
        new RendererStyle(400),
        new SvgImageBackEnd()
      );
      $writer = new Writer($renderer);

      return $writer->writeString($url);
    }

    public function verifyCode($secret, $code)
    {
      return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * リカバリーコードを生成
     */
    public function generateRecoveryCodes($count = 8)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::random(10) . '-' . Str::random(10);
        }
        return $codes;
    }

    /**
     * リカバリーコードを検証
     */
    public function verifyRecoveryCode($user, $code)
    {
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true) ?? [];
        
        if (in_array($code, $recoveryCodes)) {
            // 使用されたコードを削除
            $recoveryCodes = array_diff($recoveryCodes, [$code]);
            $user->update([
                'two_factor_recovery_codes' => json_encode(array_values($recoveryCodes))
            ]);
            return true;
        }
        
        return false;
    }
}