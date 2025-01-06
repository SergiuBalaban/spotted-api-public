<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        return $next($request)->header('Access-Control-Allow-Origin', '*');
    }
}
