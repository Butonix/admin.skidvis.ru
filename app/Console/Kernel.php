<?php

namespace App\Console;

use App\Console\Commands\BiglionParse;
use App\Console\Commands\OrganizationsExportPhoneAndEmail;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\RemoveUnusedImages;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BiglionParse::class,
        OrganizationsExportPhoneAndEmail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->job(RemoveUnusedImages::class)->cron('0 * * * *');
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
