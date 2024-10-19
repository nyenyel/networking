<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\WeeklyDashboardMonitoring;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $weekly_monitoring = WeeklyDashboardMonitoring::where('id', 1)->first();
        $wallet = Wallet::find(1);

        if (!$weekly_monitoring) {
            $this->error('Weekly monitoring record not found.');
            return;
        }
        if (!$wallet) {
            $this->error('Wallet record not found.');
            return;
        }

        DB::transaction(function () use ($weekly_monitoring, $wallet) {

            $commission = $weekly_monitoring->members_commission;

            $wallet->increment('wallet', $commission);

            $weekly_monitoring->package_sold = 0;
            $weekly_monitoring->product_purchased = 0;
            $weekly_monitoring->company_revenue = 0;
            $weekly_monitoring->members_commission = 0;

            $weekly_monitoring->save();

        });

        $this->info('Weekly fields have been reset successfully.');
    }
}
