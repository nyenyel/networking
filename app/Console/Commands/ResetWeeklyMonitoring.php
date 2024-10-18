<?php

namespace App\Console\Commands;

use App\Models\WeeklyDashboardMonitoring;
use Illuminate\Console\Command;

class ResetWeeklyMonitoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-weekly-monitoring';

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
        $monitoring = WeeklyDashboardMonitoring::where('id', 1)->first();

        if (!$monitoring) {
            $this->error('Monitoring record not found.');
            return;
        }

        $wallet = $monitoring->wallet;
        $commission = $monitoring->members_commission;

        $monitoring->package_sold = 0;
        $monitoring->product_purchased = 0;
        $monitoring->company_revenue = 0;

        $monitoring->wallet = $wallet + $commission;
        $monitoring->members_commission = 0;

        $monitoring->save();

        $this->info('Weekly fields have been reset successfully.');
    }
}
