<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\StoreInfo;
use App\Models\User\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        if (!$user) {
            DB::rollBack();
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
        if($user->admin){

            $totalMembers = $this->getTotalMembers();
            $newMembers = $this->getNewMembers();
            $dailyPackageSales = $this->getDailyPackageSales();
            $dailyProductPurchased = $this->getDailyProductPurchased();
            $openStores = $this->getOpenStores();
            $graduatedStores = $this->getGraduatedStores();
            $dailyMembersCommission = $this->getDailyMembersCommission();
            $dailyCompanyRevenue = $this->getDailyCompanyRevenue();
            $weeklySales = $this->getWeeklySales();

            // check if lumalapit na daily comm sa daily company revenue
            $isPointingSystemStopped = false;
            $threshold = 0.9; // threshold for example 90% ganon

            if ($dailyMembersCommission >= $threshold * $dailyCompanyRevenue) {
                $isPointingSystemStopped = true;
                // pede ilagay dito logic para sa stopping of pointing system
                // StoreInfo::update(['pointing_system_status' => 'stopped']);
            }

            return response()->json([
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
            ]);
        } else{
            return response()->json([
                'message' => 'Unauthorize'
            ]);
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

    protected function getDailyProductPurchased()
    {
        return \DB::table('invited_users')
            ->whereDate('created_at', now()->toDateString())
            ->count();
    }

    protected function getDailyMembersCommission()
    {
        return StoreInfo::whereDate('created_at', now()->toDateString())
            ->sum('points');
    }

    protected function getDailyCompanyRevenue()
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
}
