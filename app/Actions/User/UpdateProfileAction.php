<?php

namespace App\Actions\User;

use App\Actions\Services\ParseAvatarAction;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Tasks\User\GetAuthenticatedUserTask;
use App\Tasks\User\GetProfileResponseTask;

class UpdateProfileAction
{
    /**
     * @return array<string, array<string, int|string|null>|int|string|null>
     */
    public function run(UpdateUserRequest $request): array
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        $user->fill($request->only(User::POSSIBLE_UPDATED_FIELDS));
        $user->save();
        /** @var User $user */
        $user = app(ParseAvatarAction::class)->run($request, $user);

        return app(GetProfileResponseTask::class)->run($user);
    }
}
