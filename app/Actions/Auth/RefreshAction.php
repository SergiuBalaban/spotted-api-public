<?php

namespace App\Actions\Auth;

use App\Tasks\Auth\SetUpAuthTokenTask;
use Illuminate\Support\Facades\Auth;

class RefreshAction
{
    public function run(): string
    {
        app(SetUpAuthTokenTask::class)->run();

        return Auth::refresh();
    }
}
