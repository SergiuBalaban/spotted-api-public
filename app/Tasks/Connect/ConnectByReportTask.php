<?php

namespace App\Tasks\Connect;

use App\Models\Report;

class ConnectByReportTask
{
    public function run(Report $report): void
    {
        Report::query()
            ->where('country', $report->country)
            ->where('city', $report->city)
            ->where('category', $report->category)
            ->where('status', Report::STATUS_MISSING)
            ->get()
            ->map(function (Report $reportMissing) use ($report) {
                $chat = $report->reportChats()->firstOrNew([
                    'owner_id' => $reportMissing->user_id,
                    'reporter_id' => $report->user_id,
                    'report_missing_id' => $reportMissing->id,
                ]);
                $chat->active = 1;
                if ($chat->isDirty()) {
                    $chat->save();
                }
            });
    }
}
