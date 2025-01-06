<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(\Throwable $exception): void
    {
        parent::report($exception);
    }

    public function render($request, \Throwable $exception)
    {
        if ($exception instanceof ForbiddenException) {
            $errorDecode = json_decode($exception->getMessage(), true);
            $message = $errorDecode['message'] ?? $exception->getMessage();
            $code = $errorDecode['code'] ?? 'forbidden';

            return $this->apiErrorMessage($message, $code, Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->apiErrorMessage($exception->getMessage(), 'resourceNotFound', Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ValidationException) {
            // Add specific handling for validation exceptions if required
        }

        if ($exception instanceof AuthenticationException) {
            return $this->apiErrorMessage($exception->getMessage(), 'unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof Exception) {
            return $this->apiErrorMessage($exception->getMessage(), 'validation', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Handle all other exceptions generically
        return response()->json([
            'error' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function apiErrorMessage(string $message, string $code, int $statusCode): JsonResponse
    {

        return response()->json([
            'error' => [
                'message' => $message,
                'code' => $code,
                'status_code' => $statusCode,
            ]], $statusCode);
    }

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }
}
