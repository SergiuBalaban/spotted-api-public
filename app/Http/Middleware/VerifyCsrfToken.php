<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    ];

    public function handle($request, Closure $next)
    {
        return tap($next($request), function ($response) use ($request) {
            if ($this->shouldAddXsrfTokenCookie()) {
                $this->addCookieToResponse($request, $response);
            }
        });
    }
}
