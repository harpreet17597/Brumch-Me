<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        $schedule->command('expire_featured_subscription:cron')->timezone('UTC')->everyMinute();
        $schedule->command('expire_subscription:cron')->timezone('UTC')->everyMinute();
        $schedule->command('registration:check-expired')->timezone('UTC')->daily();
        $schedule->command('subscription-expired-every-day-cron:notification')->timezone('UTC')->dailyAt('00:01');
        $schedule->command('free-trail-expired-every-day-cron:notification')->timezone('UTC')->dailyAt('00:01');
        $schedule->call(function () {
            Artisan::call('queue:work');
        })->everyMinute();

        // $schedule->command('inspire')->hourly();
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
