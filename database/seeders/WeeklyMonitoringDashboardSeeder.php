<?php

namespace Database\Seeders;

use App\Models\WeeklyDashboardMonitoring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeeklyMonitoringDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WeeklyDashboardMonitoring::upsert([
            [
                'id' => '1',
                'package_sold' => 0,
                'product_purchased' => 0,
                'company_revenue' => 0,
                'members_commission' => 0,
                'wallet' => 0,
            ]
        ],['id']);
    }
}
