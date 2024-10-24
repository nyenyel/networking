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
use Str;

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

    public function redeemPoints(ReedemPointsRequest $reedemPointsRequest, User $userStore){

        $request = $reedemPointsRequest->validated();

        try {

            DB::beginTransaction();

            // if (Carbon::now()->isSaturday()){
            if (true){

                $user = Auth::user();

                // $user = User::where('id', 1)->first(); //for API testing

                $storeInfo = $userStore->storeInfo;
                if($storeInfo->points < $request['amount']){
                    return response()->json(['message' => 'insufficient Points '], 400);
                }

                if ($storeInfo->last_redeemed && Carbon::parse($storeInfo->last_redeemed)->isSameWeek(now())) {
                    DB::rollBack();
                    return response()->json(['message' => 'You can only redeem once a week.'], 400);
                }

                $storeInfo->points -= $request['amount'];
                $storeInfo->last_redeemed = now();

                $storeInfo->save();

                do {
                    $code = Str::random(10);
                } while (Transaction::where('code', $code)->exists());

                $transactionData = [
                    'user_id' => $user->id,
                    'transaction_type' => 1,
                    'status' => 1,
                    'amount' => $request['amount'],
                    'code' => $code
                ];

                Transaction::create($transactionData);
                // $transact = new Transaction();

                // $transact->user_id = $user->id;
                // $transact->transaction_type = 1;
                // $transact->status = 1;
                // $transact->amount = $request['amount'];

                // $transact->save();

                DB::commit();

                return response()->json(['success'=>'redeemed successfully']);

            } else {
                DB::rollBack();
                return response()->json(['message'=>'Not Saturday']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e], 500);
        }

    }

}
