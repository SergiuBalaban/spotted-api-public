<?php

namespace App\Actions\Report;

use App\Http\Requests\Report\CreateReportRequest;
use App\Models\Report;
use App\Tasks\Connect\ConnectByReportTask;
use App\Tasks\Report\CreateReportTask;

class CreateReportAction
{
    public function run(CreateReportRequest $request): Report
    {
        $reportedPetPayload = getFilledDataFromRequest((new Report)->getFillable(), $request->toArray());
        $reportedPetPayload['status'] = Report::STATUS_REPORTED;
        $report = app(CreateReportTask::class)->run($reportedPetPayload);
        app(ConnectByReportTask::class)->run($report);

        return $report;
    }
}
