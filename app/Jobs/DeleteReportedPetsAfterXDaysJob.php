<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteReportedPetsAfterXDaysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $defaultDaysBeforeDelete = now()->subDays(Report::DEFAULT_DAYS_BEFORE_DELETE);
        Report::query()
            ->where('status', Report::STATUS_REPORTED)
            ->where('created_at', '<', $defaultDaysBeforeDelete)
            ->delete();
    }
}
