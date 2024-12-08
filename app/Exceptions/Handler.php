<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @throws \Throwable
     */
    public function report(\Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param \Throwable $exception
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function render($request, \Throwable $exception)
    {
        switch ($exception) {
            case $exception instanceof ForbiddenException:
                $errorDecode = json_decode($exception->getMessage());
                $message = isset($errorDecode->message) ? $errorDecode->message : $exception->getMessage();
                $code = isset($errorDecode->code)? $errorDecode->code : 'forbidden';
                return $this->apiErrorMessage($message, $code, Response::HTTP_FORBIDDEN);
            case $exception instanceof ModelNotFoundException:
                return $this->apiErrorMessage($exception->getMessage(), 'resourceNotFound', Response::HTTP_NOT_FOUND);
            case $exception instanceof ValidationException:
                break;
            case $exception instanceof AuthenticationException:
                return $this->apiErrorMessage($exception->getMessage(), 'unauthorized', Response::HTTP_UNAUTHORIZED);
            case $exception instanceof Exception:
                return $this->apiErrorMessage($exception->getMessage(), 'validation', Response::HTTP_UNPROCESSABLE_ENTITY);
            case $exception instanceof \Exception:
                return response()->json([
                    'error' => [
                        'message' 	    => $exception->getMessage(),
                        'file'          => $exception->getFile(),
                        'line'          => $exception->getLine(),
                        'status_code'   => Response::HTTP_INTERNAL_SERVER_ERROR
                    ]
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return parent::render($request, $exception);
    }

    /**
     * @param $message
     * @param $code
     * @param $statusCode
     * @return JsonResponse
     */
    private function apiErrorMessage($message, $code, $statusCode)
    {

        return response()->json([
            'error' => [
                'message'=> $message,
                'code' => $code,
                'status_code' => $statusCode
            ]], $statusCode);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }
}
