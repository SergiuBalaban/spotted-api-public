<?php

namespace App\Actions\Pet;

use App\Actions\Services\ParseAvatarAction;
use App\Http\Requests\Pet\UpdatePetRequest;
use App\Models\Pet;

class UpdatePetAction
{
    public function run(UpdatePetRequest $request, Pet $pet): Pet
    {
        $pet->fill($request->toArray());
        $pet->save();
        /** @var Pet $pet */
        $pet = app(ParseAvatarAction::class)->run($request, $pet);

        return $pet;
    }
}
