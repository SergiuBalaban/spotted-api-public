<?php

namespace App\Actions\Report;

use App\Models\Pet;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Http\Request;

class CreateReportMissingPetAction
{
    public function run(Request $request, Pet $pet): void
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        $payload = array_merge($request->all(), [
            'user_id' => $user->id,
            'message' => $request->message,
            'category' => $pet->category,
        ]);
        $report = $pet->report()->firstOrNew();
        $report->fill($payload);
        $report->save();
    }
}
