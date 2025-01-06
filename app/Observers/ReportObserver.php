<?php

namespace App\Observers;

use App\Events\GetMissingPetsEvent;
use App\Events\ReportCreatedEvent;
use App\Events\ReportDeletedEvent;
use App\Models\Report;

class ReportObserver
{
    public function created(Report $report): void
    {
        match ($report->status) {
            Report::STATUS_MISSING => broadcast(new GetMissingPetsEvent($report->id))->toOthers(),
            Report::STATUS_REPORTED => broadcast(new ReportCreatedEvent($report->id))->toOthers(),
            default => null,
        };
    }

    public function deleted(Report $report): void
    {
        if (in_array($report->status, [Report::STATUS_MISSING, Report::STATUS_FOUND])) {
            broadcast(new GetMissingPetsEvent($report->id))->toOthers();
        }

        if ($report->status === Report::STATUS_REPORTED) {
            broadcast(new ReportDeletedEvent($report->id))->toOthers();
        }
    }
}
