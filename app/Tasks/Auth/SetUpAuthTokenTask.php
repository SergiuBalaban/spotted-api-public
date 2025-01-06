<?php

namespace App\Tasks\Auth;

use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class SetUpAuthTokenTask
{
    public function run(): void
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if (! JWTAuth::getToken()) {
            Auth::login($user);
        }
    }
}
