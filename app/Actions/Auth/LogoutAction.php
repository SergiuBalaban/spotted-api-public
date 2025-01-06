<?php

namespace App\Actions\Auth;

use App\Tasks\Auth\SetUpAuthTokenTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutAction
{
    public function run(): void
    {
        app(SetUpAuthTokenTask::class)->run();

        Auth::logout();
        Session::flush();
    }
}
