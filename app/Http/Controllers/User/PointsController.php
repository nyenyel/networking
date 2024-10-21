<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ReedemPointsRequest;
use App\Models\User;
use App\Models\User\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function redeemPoints(ReedemPointsRequest $reedemPointsRequest){

        $request = $reedemPointsRequest->validated();
    
        try {
            DB::beginTransaction();
            // if (Carbon::now()->isSaturday()){
            if (true) {  // Disable the Saturday check for now
    
                $user = Auth::user();
                $storeInfo = $user->storeInfo;
    
                // Fetch all stores and sort them by points in ascending order
                $stores = User::where('email', $user->email)->with('storeInfo')->get();
                $sortedDataByPoints = $stores->sortBy(function ($store) {
                    return $store->storeInfo->points;
                });
    
                // Calculate total points available across all stores
                $total = $sortedDataByPoints->sum(function ($store) {
                    return $store->storeInfo->points;
                });
    
                // Check if total points are enough
                if ($total < $request['amount']) {
                    return response()->json(['message' => 'Insufficient Points.'], 400);
                }
    
                // Check if the last redemption was this week
                if ($storeInfo->last_redeemed && Carbon::parse($storeInfo->last_redeemed)->isSameWeek(now())) {
                    return response()->json(['message' => 'You can only redeem once a week.'], 400);
                }
    
                $toDeduct = $total;
                

                // Deduct points from the stores in sorted order
                foreach ($sortedDataByPoints as $data) {
                    if ($toDeduct > $data->storeInfo->points) {
                        $toDeduct -= $data->storeInfo->points;
                        $data->storeInfo->points = 0; // Set points to 0
                        $data->storeInfo->save();
                    } else {
                        $data->storeInfo->points -= $toDeduct; // Deduct the remaining amount
                        $data->storeInfo->save();
                        break; // Exit loop once points are fully deducted
                    }
                }
    
                // Generate a unique code for the transaction
                do {
                    $code = Str::random(10);
                } while (Transaction::where('code', $code)->exists());
    
                // Create the transaction
                $transactionData = [
                    'user_id' => $user->id,
                    'transaction_type' => 1,
                    'status' => 1,
                    'amount' => $request['amount'],
                    'code' => $code
                ];
    
                Transaction::create($transactionData);
    
                DB::commit();
    
                return response()->json(['success' => 'Redeemed successfully']);
    
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Not Saturday']);
            }
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
