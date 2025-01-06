<?php

namespace App\Actions\Pet;

use App\Models\Pet;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetPetsAction
{
    /**
     * @return Collection<int, Pet>
     */
    public function run(Request $request): Collection
    {
        $user = app(GetAuthenticatedUserTask::class)->run();

        return $user->pets()->get();
    }
}
