<?php

namespace App\Actions\Services;

use App\Exceptions\CustomMessages\PetGalleryUploadLimitationException;
use App\Exceptions\ForbiddenException;
use App\Libraries\S3Service;
use App\Models\Pet;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ParseGalleryAction
{
    /**
     * @throws ForbiddenException
     * @throws PetGalleryUploadLimitationException
     */
    public function run(Request $request, Pet $pet): Model
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if ($request->has('file')) {
            $storage = new S3Service($user);
            $storage->createGallery($request->file, $pet);
        }

        return $pet;
    }
}
