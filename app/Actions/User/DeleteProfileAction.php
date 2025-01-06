<?php

namespace App\Actions\User;

use App\Actions\Auth\LogoutAction;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Http\Request;

class DeleteProfileAction
{
    public function run(Request $request): void
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        $user->forceDelete();
        app(LogoutAction::class)->run();
    }
}
