<?php

namespace App\Actions\Report;

use App\Models\Report;
use App\Tasks\Connect\DisconnectByReportTask;

class DeleteReportAction
{
    public function run(Report $report): void
    {
        app(DisconnectByReportTask::class)->run($report);
        $report->delete();
    }
}
