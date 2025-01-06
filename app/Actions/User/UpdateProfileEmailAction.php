<?php

namespace App\Actions\User;

use App\Http\Requests\UserEmailRequest;
use App\Models\User;
use App\Tasks\User\GetAuthenticatedUserTask;

class UpdateProfileEmailAction
{
    public function run(UserEmailRequest $request): User
    {
        $user = app(GetAuthenticatedUserTask::class)->run();

        //        Mail::to($user)->send(new SendEmailVerification($user));
        return $user;
    }
}
