<?php

namespace App\Console;

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
        //
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
        /*$schedule->call('\App\Http\Controllers\CronController@sessionClear')->everyTenMinutes();
        $schedule->call('\App\Http\Controllers\CronController@appClear')->everyTenMinutes();
        $schedule->call('\App\Http\Controllers\CronController@otpClear')->daily();
        $schedule->call('\App\Http\Controllers\CronController@recharge')->everyFifteenMinutes();*/
        
        
        $schedule->call('\App\Http\Controllers\CronController@safepayPayoutUpdate')->everyFiveMinutes();
        
        
        
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