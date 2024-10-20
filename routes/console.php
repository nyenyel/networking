<?php

use App\Console\Commands\ResetWeeklyMonitoring;
use App\Console\Commands\ResetDailyMonitoring;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(ResetDailyMonitoring::class)->dailyAt('7:55');
Schedule::command(ResetWeeklyMonitoring::class)->weeklyOn(1, '8:00');
