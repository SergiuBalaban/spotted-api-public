<?php

namespace App\Http\Middleware\Admin;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AuthenticatedSuperAdmin
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AuthorizationException
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if(!isset($user->id)) {
            throw new UnauthorizedException('You are Unauthenticated');
        }
        if(!$user->admin) {
            throw new AuthorizationException('You are not allowed');
        }

        return $next($request);
    }
}
