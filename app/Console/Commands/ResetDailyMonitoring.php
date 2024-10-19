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
        $monitoring = WeeklyDashboardMonitoring::find(2);
        $weeklyMonitoring = WeeklyDashboardMonitoring::find(1);

        if (!$monitoring || !$weeklyMonitoring) {
            $this->error('Required monitoring record(s) not found.');
            return;
        }

        $weeklyMonitoring->increment('package_sold', $monitoring->package_sold);
        $weeklyMonitoring->increment('product_purchased', $monitoring->product_purchased);
        $weeklyMonitoring->increment('company_revenue', $monitoring->company_revenue);
        $weeklyMonitoring->increment('members_commission', $monitoring->members_commission);

        $monitoring->update([
            'package_sold' => 0,
            'product_purchased' => 0,
            'company_revenue' => 0,
            'members_commission' => 0,
        ]);

        $this->info('Daily fields have been reset successfully.');
    }
}
