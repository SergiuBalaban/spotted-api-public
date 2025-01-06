<?php

namespace App\Actions\Services;

use App\Exceptions\ForbiddenException;
use App\Libraries\S3Service;
use App\Models\Pet;
use App\Models\User;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Http\Request;

class ParseAvatarAction
{
    /**
     * @throws ForbiddenException
     */
    public function run(Request $request, Pet|User $model): Pet|User
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if ($request->has('file')) {
            $storage = new S3Service($user);

            return $storage->createAvatar($request->file, $model);
        }

        return $model;
    }
}
