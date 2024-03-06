<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Enforce2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = user();
        if ($user->hasRole('admin'))
        {
            $userAuth = $user->userAuth;
            $twoFactorSetup = $userAuth->two_factor_confirmed || $userAuth->two_factor_email_confirmed;

            if (!$twoFactorSetup)
                return redirect()->route('two-fa-settings.index');
        }

        return $next($request);
    }
}
