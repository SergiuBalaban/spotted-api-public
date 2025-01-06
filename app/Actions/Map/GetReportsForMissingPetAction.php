<?php

namespace App\Actions\Map;

use App\Models\Pet;
use App\Models\Report;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class GetReportsForMissingPetAction
{
    /**
     * @return Collection<int, Report>
     */
    public function run(Request $request): Collection
    {
        $user = app(GetAuthenticatedUserTask::class)->run();

        /** @var ?Pet $missingPet */
        $missingPet = $user->missingPets()->first();
        if (! $missingPet) {
            return Collection::empty();
        }

        return Report::query()
            ->where('city', $request->city)
            ->where('category', $missingPet->category)
            ->where('status', Report::STATUS_REPORTED)
            ->orderByDesc('id')
            ->get();
    }
}
