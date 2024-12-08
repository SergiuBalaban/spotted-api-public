<?php

namespace App\Console\Commands;

use App\Jobs\DeleteReportedPetsAfterXDaysJob;
use Illuminate\Console\Command;

class CronDeleteReportedPets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:deleteReportedPetsAfterXDays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Reported pets after x days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DeleteReportedPetsAfterXDaysJob::dispatch();
    }
}
