<?php

namespace App\Http\Controllers\Admin;

use App\Events\DashboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\StoreInfo;
use App\Models\User\Transaction;
use App\Models\WeeklyDashboardMonitoring;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function showDashboard()
{
    // Authenticate user
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    // Check if user is an admin
    if ($user->admin) {
        // Fetch data for the dashboard
        $totalMembers = $this->getTotalMembers();
        $newMembers = $this->getNewMembers();
        $dailyPackageSales = $this->getDailyPackageSales();
        $dailyProductPurchased = $this->getDailyProductPurchased();
        $openStores = $this->getOpenStores();
        $graduatedStores = $this->getGraduatedStores();
        $dailyMembersCommission = $this->getDailyMembersCommission();
        $dailyCompanyRevenue = $this->getDailyCompanyRevenue();
        $weeklySales = $this->getWeeklySales();
        $weeklyDashboard = $this->weeklyDashboard();

        // Logic to check pointing system status
        $isPointingSystemStopped = false;
        $threshold = 0.9; // Threshold set to 90%

        if ($dailyMembersCommission >= $threshold * $dailyCompanyRevenue) {
            $isPointingSystemStopped = true;
            // Optional: Logic to stop the pointing system
            // StoreInfo::update(['pointing_system_status' => 'stopped']);
        }

        // Prepare the dashboard data array
        $dashboardData = [
            'totalMembers' => $totalMembers,
            'newMembers' => $newMembers,
            'dailyPackageSales' => $dailyPackageSales,
            'dailyProductPurchased' => $dailyProductPurchased,
            'dailyMembersCommission' => $dailyMembersCommission,
            'dailyCompanyRevenue' => $dailyCompanyRevenue,
            'openStores' => $openStores,
            'graduatedStores' => $graduatedStores,
            'weeklySales' => $weeklySales,
            'isPointingSystemStopped' => $isPointingSystemStopped,
            'weeklyDashboard' => $weeklyDashboard,
        ];

        // Broadcast the updated dashboard data
        broadcast(new DashboardUpdated($dashboardData));

        // Return the dashboard data as JSON response
        return response()->json($dashboardData);
    } else {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}


    public function getTotalMembers()
    {
        return User::count();
    }

    public function getNewMembers()
    {
        return User::whereDate('created_at', Carbon::today())->count(); // today only
    }

    public function getDailyPackageSales()
    {
        return StoreInfo::whereDate('created_at', Carbon::today())->count() * 5000;
    }

    public function getDailyProductPurchased()
    {
        return DB::table('invited_users')
            ->whereDate('created_at', now()->toDateString())
            ->count();
    }

    public function getDailyMembersCommission()
    {
        return StoreInfo::whereDate('created_at', now()->toDateString())
            ->sum('points');
    }

    public function getDailyCompanyRevenue()
    {
        return Transaction::where('status', 1)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
    }

    public function getOpenStores()
    {
        return StoreInfo::where('status', 2)->count();
    }

    public function getGraduatedStores()
    {
        return StoreInfo::where('status', 3)->count();
    }

    public function getWeeklySales()
    {
        return StoreInfo::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count() * 5000;
    }
    public function weeklyDashboard(){
        return WeeklyDashboardMonitoring::first();
    }
}
