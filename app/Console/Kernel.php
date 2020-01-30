<?php

namespace App\Console;

use App\Console\Commands\SpoolRegistrationRequests;
use App\Console\Commands\PurgeRegistrationRequests;
use App\Console\Commands\SendMailSpool;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        PurgeRegistrationRequests::class,
        SendMailSpool::class,
        SpoolRegistrationRequests::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
