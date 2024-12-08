<?php

namespace App\Http\Middleware\User;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class CheckUserBannedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = auth()->user();
        if($user->banned) {
            if(Carbon::now() < $user->banned_at) {
                $time = Carbon::now()->diffInMinutes($user->banned_at);
                throw new AuthorizationException('You have been banned for '. $time.' minutes due to policy violation');
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
