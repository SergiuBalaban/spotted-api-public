<?php

namespace App\Actions\Map;

use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class GetMissingPetsAction
{
    /**
     * @return Collection<int, Report>
     */
    public function run(Request $request): Collection
    {
        return Report::query()
            ->where('city', $request->city)
            ->where('status', Report::STATUS_MISSING)
            ->orderByDesc('id')
            ->get();
    }
}
