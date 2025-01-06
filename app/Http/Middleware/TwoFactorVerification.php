<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorVerification
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (app()->isLocal()) {
            return $next($request);
        }
        //        $user = auth()->user();

        //        if (! is_null($user->token_2fa) || ! is_null($user->token_2fa_expiry)) {
        //            throw new UnauthorizedException;
        //        }

        return $next($request);
    }
}
