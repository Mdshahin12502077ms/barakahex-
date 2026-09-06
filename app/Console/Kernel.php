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
        // 'App\Console\Commands\DatabaseBackUp'
        \App\Console\Commands\DailyPaymentRequest::class,
        \App\Console\Commands\DatabaseBackUp::class,
        \App\Console\Commands\MonthlyPaymentRequest::class,
        \App\Console\Commands\ShortUrlCommand::class,
        \App\Console\Commands\WeeklyPaymentRequest::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('daily:payment-request')->daily();
        $schedule->command('weekly:payment-request')->weekly();
        $schedule->command('monthly:payment-request')->monthly();
        $schedule->command('inspire')->hourly();
        $schedule->command('generate:short-url')->everyMinute();
        $schedule->command('database:backup')->daily();

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
