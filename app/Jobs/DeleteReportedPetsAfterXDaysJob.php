<?php

namespace App\Jobs;

use App\Models\ReportedPet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteReportedPetsAfterXDaysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?User $user;

    /**
     * DeleteReportedPetsAfterXDaysJob constructor.
     *
     * @param User|null $user
     */
    public function __construct(User $user=null)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $defaultDaysBeforeDelete = Carbon::now()->subDays(ReportedPet::DEFAULT_DAYS_BEFORE_DELETE);
        $reportedPets = ReportedPet::where('status', ReportedPet::STATUS_REPORTED)->where('created_at', '<', $defaultDaysBeforeDelete);
        $reportedPets->each(function (ReportedPet $reportedPet) {
            $reportedPet->trackedPets()->delete();
        });
        $reportedPets->delete();
    }
}
