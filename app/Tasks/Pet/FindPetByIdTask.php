<?php

namespace App\Tasks\Pet;

use App\Exceptions\NotFoundException;
use App\Models\Pet;

class FindPetByIdTask
{
    /**
     * @throws NotFoundException
     */
    public function run(int $id): Pet
    {
        try {
            return Pet::query()->withTrashed()->findOrFail($id);
        } catch (\Exception $exception) {
            report($exception);
            throw (new NotFoundException('Error Finding Pet'));
        }
    }
}
