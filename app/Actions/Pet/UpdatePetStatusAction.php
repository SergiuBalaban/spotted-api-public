<?php

namespace App\Actions\Pet;

use App\Actions\Report\CreateReportMissingPetAction;
use App\Http\Requests\Pet\UpdatePetStatusRequest;
use App\Models\Pet;
use App\Tasks\Connect\ConnectByPetTask;
use App\Tasks\Connect\DisconnectByPetTask;

class UpdatePetStatusAction
{
    public function run(UpdatePetStatusRequest $request, Pet $pet): void
    {
        $pet->update(['status' => $request->status]);
        if ($request->status === Pet::STATUS_MISSING) {
            app(CreateReportMissingPetAction::class)->run($request, $pet);
            app(ConnectByPetTask::class)->run($pet);
        }
        if ($request->status === Pet::STATUS_FOUND) {
            app(DisconnectByPetTask::class)->run($pet);
            $pet->report()->delete();
        }
    }
}
