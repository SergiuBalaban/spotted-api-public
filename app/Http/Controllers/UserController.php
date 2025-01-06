<?php

namespace App\Http\Controllers;

use App\Actions\User\DeleteProfileAction;
use App\Actions\User\GetProfileAction;
use App\Actions\User\UpdateProfileAction;
use App\Actions\User\UpdateProfileEmailAction;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserEmailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function getProfile(Request $request): JsonResponse
    {
        $response = app(GetProfileAction::class)->run($request);

        return response()->json($response);
    }

    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $response = app(UpdateProfileAction::class)->run($request);

        return response()->json($response);
    }

    public function updateEmail(UserEmailRequest $request): JsonResponse
    {
        $user = app(UpdateProfileEmailAction::class)->run($request);

        return response()->json($user);
    }

    public function deleteProfile(Request $request): Response
    {
        app(DeleteProfileAction::class)->run($request);

        return response()->noContent();
    }
}
