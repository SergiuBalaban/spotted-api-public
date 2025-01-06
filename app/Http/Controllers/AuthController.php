<?php

namespace App\Http\Controllers;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\RefreshAction;
use App\Actions\Auth\ValidateAuthSmsAction;
use App\Http\Requests\VerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function verification(VerificationRequest $request): JsonResponse
    {
        $response = app(ValidateAuthSmsAction::class)->run($request);

        return response()->json($response);
    }

    public function login(VerificationRequest $request): JsonResponse
    {
        $token = app(LoginAction::class)->run($request);

        return $this->setJwtToken($token);
    }

    public function logout(): Response
    {
        app(LogoutAction::class)->run();

        return response()->noContent();
    }

    public function refresh(): JsonResponse
    {
        $token = app(RefreshAction::class)->run();

        return $this->setJwtToken($token);
    }
}
