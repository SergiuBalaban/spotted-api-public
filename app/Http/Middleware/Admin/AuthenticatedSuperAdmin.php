<?php

namespace App\Http\Middleware\Admin;

use App\Exceptions\UnauthorizedException;
use App\Tasks\User\GetAuthenticatedUserTask;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticatedSuperAdmin
{
    /**
     * @throws AuthorizationException
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if (! isset($user->id)) {
            throw new UnauthorizedException('You are Unauthenticated');
        }
        if (! $user->admin) {
            throw new AuthorizationException('You are not allowed');
        }

        return $next($request);
    }
}
