<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:reset-daily-monitoring')->dailyAt('05:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}