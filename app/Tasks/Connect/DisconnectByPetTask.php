<?php

namespace App\Tasks\Connect;

use App\Models\Pet;
use App\Tasks\Pet\GetPetReportTask;

class DisconnectByPetTask
{
    public function run(Pet $pet): void
    {
        $reportMissing = app(GetPetReportTask::class)->run($pet);
        if (! $reportMissing) {
            return;
        }
        // Disconnect chats
        $reportMissing->missingPetChats()->where('active', 1)->update(['active' => 0]);
    }
}
