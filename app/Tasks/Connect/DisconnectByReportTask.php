<?php

namespace App\Tasks\Connect;

use App\Models\Report;

class DisconnectByReportTask
{
    public function run(Report $report): void
    {
        $report->reportChats()->where('active', 1)->update(['active' => 0]);
    }
}
