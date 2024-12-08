<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorVerification
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return JsonResponse|mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if(app()->isLocal()){
            return $next($request);
        }
        $user = auth()->user();

        if(!is_null($user->token_2fa) || !is_null($user->token_2fa_expiry)){
            throw new UnauthorizedException();
        }
        return $next($request);
    }
}
