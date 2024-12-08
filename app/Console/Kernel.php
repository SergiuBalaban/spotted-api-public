<?php

namespace App\Console;

use App\Console\Commands\CronDeleteReportedPets;
use App\Console\Commands\CronResetBannedUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CronDeleteReportedPets::class,
        CronResetBannedUsers::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:deleteReportedPetsAfterXDays')->dailyAt('23:00');
//        $schedule->command('cron:resetBannedUsers')->monthlyOn('23:00');
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
