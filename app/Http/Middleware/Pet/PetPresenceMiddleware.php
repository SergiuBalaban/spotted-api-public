<?php

namespace App\Http\Middleware\Pet;

use App\Exceptions\UnauthorizedException;
use App\Tasks\User\GetAuthenticatedUserTask;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetPresenceMiddleware
{
    /**
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if (! $user->pets()->where('id', $request->pet->id)->exists()) {
            throw new UnauthorizedException('You are unauthorized');
        }
        $response = $next($request);

        return $response instanceof JsonResponse
            ? $response
            : response()->json($response->getOriginalContent(), $response->getStatusCode());
    }
}
