<?php

namespace App\Tasks\Connect;

use App\Models\Pet;
use App\Models\Report;
use App\Tasks\Pet\GetPetReportTask;

class ConnectByPetTask
{
    public function run(Pet $pet): void
    {
        $reportMissing = app(GetPetReportTask::class)->run($pet);
        if (! $reportMissing) {
            return;
        }
        // Connect chats
        Report::query()
            ->where('country', $reportMissing->country)
            ->where('city', $reportMissing->city)
            ->where('category', $reportMissing->category)
            ->where('status', Report::STATUS_REPORTED)
            ->get()
            ->map(function (Report $report) use ($pet, $reportMissing) {
                $chat = $reportMissing->missingPetChats()->firstOrNew([
                    'owner_id' => $pet->user_id,
                    'reporter_id' => $report->user_id,
                    'report_found_id' => $report->id,
                ]);
                $chat->active = 1;
                if ($chat->isDirty()) {
                    $chat->save();
                }
            });
    }
}
