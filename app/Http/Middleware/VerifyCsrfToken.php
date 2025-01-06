<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\JsonResponse;

class VerifyCsrfToken extends Middleware
{
    /**
     * @var bool
     */
    protected $addHttpCookie = true;

    public function handle(mixed $request, Closure $next): JsonResponse
    {
        return tap($next($request), function ($response) use ($request) {
            if ($this->shouldAddXsrfTokenCookie()) {
                $this->addCookieToResponse($request, $response);
            }
        });
    }
}
