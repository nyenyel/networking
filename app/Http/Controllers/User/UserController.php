<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteUserRequest;
use App\Models\User;
use App\Models\User\InvitedUser;
use App\Models\User\StoreInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
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

    public function AddUser(InviteUserRequest $request)
    {
        $valid_req = $request->validated();
        $valid_req['password'] = bcrypt($valid_req['password']);

        $user = User::create($valid_req);

        // Get the ID of the user who is inviting (use Auth::user()->id in real scenario)
        $invitedById = 1; // for API testing, replace with the actual inviter's user ID

        $defaultStoreInfo = [
            'user_id' => $user->id,
            'invited_by' => $invitedById,
            'points' => 0,
            'unpaid' => 1,
            'status' => 0,
        ];

        $store_info = StoreInfo::create($defaultStoreInfo);

        // record the invitation
        $this->recordInvitation($invitedById, $user->id);

        // update inviter's store info
        $this->updateInviterStoreInfo($invitedById);

        return response()->json([
            'message' => 'success',
            'user' => $user,
            'default_store_info' => $store_info
        ]);
    }

    protected function recordInvitation($invitedById, $newUserId)
    {
        // record  invitation in the invited_users table
        InvitedUser::create([
            'user_id' => $invitedById,
            'invited_user' => $newUserId,
        ]);
    }

    protected function updateInviterStoreInfo($invitedById)
    {
        // find the store info of the inviter
        $inviterStoreInfo = StoreInfo::where('user_id', $invitedById)->first();

        if ($inviterStoreInfo) {
            // Increment the invited_users_count
            $inviterStoreInfo->increment('invited_users_count');

            // Update status to 2 if 2 users have been invited
            if ($inviterStoreInfo->invited_users_count >= 2) {
                $inviterStoreInfo->status = 2;
                $inviterStoreInfo->save();

                $this->passPointsToParentStore($invitedById);
            }
        }
    }

    protected function passPointsToParentStore($userId)
    {
        // get  inviter of the current user
        $inviterStoreInfo = StoreInfo::where('user_id', $userId)->first();

        if ($inviterStoreInfo) {
            $parentId = $inviterStoreInfo->invited_by;

            if ($parentId) {
                // get the par ent's store info
                $parentStoreInfo = StoreInfo::where('user_id', $parentId)->first();

                if ($parentStoreInfo) {
                    // pass points to the parent store
                    $parentStoreInfo->points += 10;
                    $parentStoreInfo->save();

                    if($parentStoreInfo->points >= 5000){
                        $parentStoreInfo->status = 3; //graduate
                        $parentStoreInfo->save();

                        StoreInfo::where('invited_by', $parentId)
                                ->update(['invited_by' => null]);
                    }

                    // recursively pass points to the grandparent store, etc.
                    $this->passPointsToParentStore($parentId);
                }
            }
        }
    }
}
