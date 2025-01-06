<?php

namespace App\Actions\Services;

use App\Libraries\S3Service;
use App\Models\Pet;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Http\Request;

class DeleteGalleryAction
{
    public function run(Request $request, Pet $pet): Pet
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        $storage = new S3Service($user);

        return $storage->removeGallery($pet, $request->get('files'));
    }
}
