<?php

namespace App\Actions\Pet;

use App\Actions\Services\ParseAvatarAction;
use App\Exceptions\CustomMessages\OnePetCreationException;
use App\Http\Requests\Pet\CreatePetRequest;
use App\Models\Pet;
use App\Tasks\User\GetAuthenticatedUserTask;

class CreatePetAction
{
    /**
     * @throws OnePetCreationException
     */
    public function run(CreatePetRequest $request): Pet
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        if ($user->pets()->count() >= 1) {
            throw new OnePetCreationException;
        }
        $pet = $user->pets()->create($request->only(Pet::POSSIBLE_UPDATED_FIELDS));
        /** @var Pet $pet */
        $pet = app(ParseAvatarAction::class)->run($request, $pet);

        return $pet;
    }
}
