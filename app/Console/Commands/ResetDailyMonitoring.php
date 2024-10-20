<?php

namespace App\Console\Commands;

use App\Models\WeeklyDashboardMonitoring;
use Illuminate\Console\Command;

class ResetDailyMonitoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-daily-monitoring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monitoring = WeeklyDashboardMonitoring::where('id', 2)->first();
        $weeklyMonitoring = WeeklyDashboardMonitoring::find(1);

        if (!$monitoring || !$weeklyMonitoring) {
            $this->error('Required monitoring record(s) not found.');
            return;
        }

        $monitoring->package_sold = 0;
        $monitoring->product_purchased = 0;
        $monitoring->company_revenue = 0;
        $monitoring->members_commission = 0;
        $monitoring->save();

        $this->info('Daily fields have been reset successfully.');
    }
}
