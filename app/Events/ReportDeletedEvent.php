<?php

namespace App\Events;

use App\Models\Report;
use App\Tasks\Report\FindReportByIdTask;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportDeletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected Report $report;

    public function __construct(int $reportId)
    {
        $this->report = app(FindReportByIdTask::class)->run($reportId);
    }

    public function broadcastOn(): Channel
    {
        return new Channel("$this->report->category.$this->report->city.");
    }

    public function broadcastAs(): string
    {
        return 'reportDeleted';
    }
}
