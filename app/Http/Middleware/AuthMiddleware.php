<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Tasks\User\GetAuthenticatedUserTask;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthMiddleware
{
    /**
     * @throws ForbiddenException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if (! $user->active) {
            throw new ForbiddenException('This user is not active');
        }

        $response = $next($request);

        return $response instanceof JsonResponse
            ? $response
            : response()->json($response->getOriginalContent(), $response->getStatusCode());
    }
}
