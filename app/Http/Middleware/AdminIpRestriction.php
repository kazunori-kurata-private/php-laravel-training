<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIpRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = config('admin.allowed_ips', null);
        $clientIp = $request->ip();

        // 特定のIPアドレスチェック
        foreach ($allowedIps as $allowedIp) {
            if ($this->isIpAllowed($clientIp, $allowedIp)) {
                return $next($request);
            }
        }

        // IPが許可されていない場合
        abort(403, 'このIPアドレスからの管理画面へのアクセスは許可されていません。');
    }

    /**
     * IPアドレスが許可されているかチェック
     *
     * @param string $clientIp
     * @param string $allowedIp
     * @return bool
     */
    private function isIpAllowed(string $clientIp, string $allowedIp): bool
    {
        // CIDR表記のチェック
        if (str_contains($allowedIp, '/')) {
            return $this->isIpInCidrRange($clientIp, $allowedIp);
        }

        // 単一IPアドレスのチェック
        return $clientIp === $allowedIp;
    }

    /**
     * CIDR範囲内のIPかチェック
     *
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    private function isIpInCidrRange(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);
        
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);
        
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
} 