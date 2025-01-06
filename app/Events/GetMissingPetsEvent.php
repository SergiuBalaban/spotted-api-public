<?php

namespace App\Events;

use App\Models\Report;
use App\Tasks\Report\FindReportByIdTask;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GetMissingPetsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected Report $report;

    public function __construct(int $reportId)
    {
        $this->report = app(FindReportByIdTask::class)->run($reportId);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('city.'.$this->report->city.'.missingPets');
    }

    public function broadcastAs(): string
    {
        return 'getMissingPets';
    }
}
