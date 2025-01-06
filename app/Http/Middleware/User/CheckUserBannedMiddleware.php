<?php

namespace App\Http\Middleware\User;

use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckUserBannedMiddleware
{
    /**
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->banned) {
            if (now() < $user->banned_at) {
                $time = now()->diffInMinutes($user->banned_at);
                throw new AuthorizationException('You have been banned for '.$time.' minutes due to policy violation');
            }
            $user->update([
                'banned' => false,
                'banned_count' => 0,
                'banned_at' => null,
            ]);
        }

        return $next($request);
    }
}
