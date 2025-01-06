<?php

namespace App\Console\Commands;

use App\Jobs\ResetBannedUsersJob;
use Illuminate\Console\Command;

class CronResetBannedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:resetBannedUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset banned users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        ResetBannedUsersJob::dispatch();
    }
}
