<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteUserRequest;
use App\Models\User;
use App\Models\User\InvitationCode;
use App\Models\User\InvitedUser;
use App\Models\User\StoreInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Str;

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
        // Validate the request data
        $valid_req = $request->validated();
        $valid_req['password'] = bcrypt($valid_req['password']);

        // Check if an invitation code was provided
        if (isset($valid_req['invitation_code'])) {
            // Validate the invitation code
            $invitationCode = InvitationCode::where('code', $valid_req['invitation_code'])->first();

            if (!$invitationCode) {
                return response()->json(['message' => 'Invalid invitation code'], 400);
            }

            // Check if the code has been used more than twice
            if ($invitationCode->used_count >= 2) {
                return response()->json(['message' => 'Invitation code has already been used 2 times'], 400);
            }

            // Get the inviter's user ID from the invitation code
            $invitedById = $invitationCode->user_id;
        } else {
            return response()->json(['message' => 'Invitation code is required'], 400);
        }

        // Create the new user
        $user = User::create($valid_req);

        // Set default store information for the new user
        $defaultStoreInfo = [
            'user_id' => $user->id,
            'invited_by' => $invitedById,
            'points' => 0,
            'points_limit' => 0,
            'unpaid' => 1,
            'status' => 0,
            'invitation_code' => $valid_req['invitation_code'], // Save inviter's invitation code here
        ];

        // Create store info for the new user
        $store_info = StoreInfo::create($defaultStoreInfo);

        // Record the invitation (link inviter and invitee using the inviter's user ID)
        $this->recordInvitation($valid_req['invitation_code'], $user->id);

        // Update inviter's store info (you can still use inviter's user_id)
        $this->updateInviterStoreInfo($invitedById);

        // Increment the used count for the invitation code
        $invitationCode->increment('used_count');

        // Generate and save the unique invitation code for the new user
        $newInvitationCode = $this->generateInvitationCodeForUser($user->id);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'default_store_info' => $store_info,
            'invitation_code' => $newInvitationCode // Return the newly created invitation code
        ]);
    }

    protected function generateInvitationCodeForUser($userId)
    {
        // Generate a unique random string as the invitation code
        $code = Str::random(10);

        // Store the code with the user's ID in the invitation_codes table
        $invitationCode = InvitationCode::create([
            'user_id' => $userId,
            'code' => $code,
            'used_count' => 0, // Initialize used count to 0
        ]);

        return $code; // Return the generated code
    }


    protected function recordInvitation($invitationCode, $newUserId)
    {
        // Get the inviter's user_id using the provided invitation code
        $inviter = InvitationCode::where('code', $invitationCode)->first();

        if ($inviter) {
            // Record the invitation using the inviter's user_id (not the code)
            InvitedUser::create([
                'user_id' => $inviter->user_id, // The actual user_id of the inviter
                'invited_user' => $newUserId,
            ]);
        } else {
            // Handle the case where the inviter is not found
            throw new \Exception('Invalid invitation code.');
        }
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
                    $parentStoreInfo->points_limit += 10;
                    $parentStoreInfo->save();

                    if($parentStoreInfo->points_limit >= 5000){
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

    // Method to get the genealogy (both ancestors and descendants)
    public function getGenealogy($userId)
    {
        $user = User::findOrFail($userId);

        // Get ancestors (all users who invited the current user)
        $ancestors = $this->getAncestors($user);

        // Get descendants (all users invited by the current user and their invitees)
        $descendants = $this->getDescendants($user);

        return response()->json([
            'user' => $user,
            'ancestors' => $ancestors,
            'descendants' => $descendants,
        ]);
    }

    // Recursive function to get all ancestors (users who invited the user)
    protected function getAncestors($user)
    {
        $ancestors = [];
        $currentUser = $user;

        while ($currentUser->storeInfo && $currentUser->storeInfo->invited_by) {
            $inviter = User::find($currentUser->storeInfo->invited_by);
            if ($inviter) {
                $ancestors[] = $inviter;
                $currentUser = $inviter;
            } else {
                break;
            }
        }

        return $ancestors;
    }

    // Recursive function to get all descendants (users invited by this user and their invitees)
    protected function getDescendants($user)
    {
        $descendants = [];

        $invitedUsers = InvitedUser::where('user_id', $user->id)->get();

        foreach ($invitedUsers as $invitedUser) {
            $invited = User::find($invitedUser->invited_user);
            if ($invited) {
                $descendants[] = $invited;

                // Recursively get invitees of the invited user
                $childDescendants = $this->getDescendants($invited);
                $descendants = array_merge($descendants, $childDescendants);
            }
        }

        return $descendants;
    }
}
