<?php

namespace App\Tasks\User;

use App\Exceptions\ForbiddenException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetAuthenticatedUserTask
{
    /**
     * @throws ForbiddenException
     */
    public function run(): User
    {
        $user = Auth::user();
        if (! $user) {
            throw (new ForbiddenException('You are not allowed'));
        }

        return $user;
    }
}
