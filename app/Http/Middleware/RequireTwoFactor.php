<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;

class RequireTwoFactor
{
  public function handle(Request $request, Closure $next)
  {
    $user = $request->user();
    
    if ($user && 
      Features::enabled(Features::twoFactorAuthentication()) &&
      $user->two_factor_secret &&
      $user->two_factor_confirmed_at &&
      !$request->session()->has('2fa_verified')) {
      
      return redirect()->route('admin.two-factor.challenge');
    }
    
    return $next($request);
  }
}