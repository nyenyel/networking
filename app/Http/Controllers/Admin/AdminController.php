<?php

namespace App\Http\Controllers\Admin;

use App\Events\DashboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\User\StoreInfo;
use App\Models\User\Transaction;
use App\Models\Wallet;
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
        $dailyDashboard = $this->dailyDashboard();

        // Logic to check pointing system status
        $isPointingSystemStopped = false;
        $threshold = 0.5; // Threshold set to 90%

        $setting = Setting::where('id', 1)->first();
        if ($weeklyDashboard->members_commission >= $threshold * $weeklyDashboard->company_revenue) {
            
            $isPointingSystemStopped = true;

            $setting->special_feature = true;
            $setting->save();
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
            'dailyDashboard' => $dailyDashboard,
            'switch' => $setting->special_feature
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
        return StoreInfo::whereDate('created_at', Carbon::today())->count() * 2000;
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
        return StoreInfo::where('status', 1)->count();
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

    public function approveRedeemRequest(Request $request)
    {
        $validated = $request->validate(['code' => 'required|exists:transactions,code']);
        
        $code = $validated['code'];
        $redeemRequest = Transaction::where('code', $code)->first();
        $weeklyrecord = WeeklyDashboardMonitoring::findOrFail(1);
        $dailyRecord = WeeklyDashboardMonitoring::findOrFail(2);
        $user = User::findOrFail($redeemRequest->user_id);
        if($redeemRequest->status === 0){
            $redeemRequest->update([
                'status'=>1
            ]);
    
            $weeklyrecord->decrement('members_commission', $redeemRequest->amount);
            $dailyRecord->decrement('members_commission', $redeemRequest->amount);
    
            return response()->json(['message'=>'successfully redeemed']);
        } else {
            return response()->json(['message'=>'Code Already Claimed'] ,400);

        }
    }

    public function rejectRedeemRequest($id)
    {
        $redeemRequest = Transaction::findOrFail($id);
        $redeemRequest->update([
            'status'=>2
        ]);
        return response()->json(['message'=>'request rejected']);
    }
    public function weeklyDashboard(){
        return WeeklyDashboardMonitoring::where('id', 1)->first();
    }

    public function dailyDashboard(){
        return WeeklyDashboardMonitoring::where('id', 2)->first();
    }

    public function updateSpecial(){
        $setting = Setting::where('id', 1)->first();
        $setting->special_feature = !$setting->special_feature;
        $setting->save();
        return response()->json(['message' => 'updated']);
    }

    public function wallet(){
        $user = Auth::user();
        if($user->admin){
            $unclaimed = Wallet::findOrFail(1)->first();
            $weeklyCommision = WeeklyDashboardMonitoring::findOrFail(1)->first();
            $transaction = Transaction::where('transaction_type', 1)
                                        ->where('status', false)
                                        ->get();
            $transaction->load(['users']);
            return response()->json([
                'unclaimed' => $unclaimed->wallet,
                'weekly' => $weeklyCommision->members_commission,
                'transaction' => $transaction
            ]);
        } else {
            return response()->json(['message'=> 'Unauthorized']);
        }


    }
}
