<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Models\User;
use Closure;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws ForbiddenException
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = auth()->user();
        if(!isset($user->id) || !$user->active) {
            throw new ForbiddenException('This user is not active');
        }

        return $next($request);
    }
}
