<?php

namespace App\Actions\User;

use App\Tasks\User\GetAuthenticatedUserTask;
use App\Tasks\User\GetProfileResponseTask;
use Illuminate\Http\Request;

class GetProfileAction
{
    /**
     * @return array<string, array<string, int|string|null>|int|string|null>
     */
    public function run(Request $request): array
    {
        $user = app(GetAuthenticatedUserTask::class)->run();

        return app(GetProfileResponseTask::class)->run($user);
    }
}
