<?php

namespace App\Http\Controllers\User;

use App\Events\DashboardUpdated;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Sequence;
use App\Models\Setting;
use App\Models\User;
use App\Models\User\InvitationCode;
use App\Models\User\InvitedUser;
use App\Models\User\StoreInfo;
use App\Models\WeeklyDashboardMonitoring;
use Carbon\Carbon;
use Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use  Illuminate\Support\Str;

use function Pest\Laravel\json;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            DB::rollBack();
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
        if($user->admin){
            $data = User::where('store_no', 0)->get();
            $data->load(['inviteCode', 'storeInfo']);
            return response()->json($data);
        } else{
            return response()->json(['message' => 'Unauthorized']);
        }
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
            $authUser = Auth::user();
            if(!$authUser->admin){
                return response()->json(['message' => 'Unaauthorize Account'], 400);
            }
            // Validate the request data
            $valid_req = $request->validated();

            $valid_req['password'] = bcrypt($valid_req['password']);

            // Initialize $invitedById as null in case there's no invitation code
            $invitedById = null;

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

                // Increment the used count for the invitation code
                $invitationCode->increment('used_count');
            }

            // Create the new user
            $user = User::create($valid_req);

            // Set default store information for the new user
            $defaultStoreInfo = [
                'user_id' => $user->id,
                'invited_by' => $invitedById, // Could be null if no invitation code was provided
                'points' => 0,
                'points_limit' => 0,
                'unpaid' => 1,
                'status' => 0,
                'invitation_code' => $valid_req['invitation_code'] ?? null, // Save inviter's invitation code if available
            ];

            // Create store info for the new user
            $store_info = StoreInfo::create($defaultStoreInfo);

            // If an invitation code was provided, record the invitation and update inviter's info
            if ($invitedById) {
                // Record the invitation (link inviter and invitee using the inviter's user ID)
                $this->recordInvitation($valid_req['invitation_code'], $user->id);

                // Update inviter's store info
                $this->updateInviterStoreInfo($invitedById);
            }

            // Generate and save the unique invitation code for the new user
            $newInvitationCode = $this->generateInvitationCodeForUser($user->id);

            $monitoring = WeeklyDashboardMonitoring::where('id', 2)->first(); //daily

            $monitoring->package_sold += 1;
            $monitoring->product_purchased += 500;
            $monitoring->company_revenue += 1500;

            $monitoring->save();
            $this->realtimeEvent();
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
                $inviterStoreInfo->status = 1;
                $inviterStoreInfo->save();
                $newSequence = Sequence::create([
                    'current_user_id' => $invitedById,
                    'before_user_id' => $invitedById
                ]);
                $this->sequencePassing($newSequence);
                // $this->passPointsToFirstOpenedStore($invitedById);
            }
        }
    }

    protected function updateStoreInviterInfo($invitedById, $invitingStoreId)
    {
        // Find the store info of the inviter using the inviting store ID passed
        $inviterStoreInfo = StoreInfo::where('user_id', $invitedById)
                                    ->where('id', $invitingStoreId)
                                    ->first();

        if ($inviterStoreInfo) {
            // Increment the invited_users_count
            $inviterStoreInfo->increment('invited_users_count');

            // Update status to 2 if 2 users have been invited
            if ($inviterStoreInfo->invited_users_count >= 2) {
                $inviterStoreInfo->status = 2;
                $inviterStoreInfo->save();

                // Pass points to the parent store using the inviting store ID
                $this->passPointsToParentStoreByStoreId($invitingStoreId);
            }
        }
    }

    protected function passPointsToParentStoreByStoreId($invitingStoreId)
    {
        // Get the store info using the provided store ID
        $inviterStoreInfo = StoreInfo::find($invitingStoreId);


        if ($inviterStoreInfo) {
            $parentId = $inviterStoreInfo->invited_by;

            if ($parentId) {
                // Get the parent's store info
                $parentStoreInfo = StoreInfo::where('user_id', $parentId)->first();

                if ($parentStoreInfo) {
                    // Pass points to the parent store
                    $parentStoreInfo->points += 10;
                    $parentStoreInfo->points_limit += 10;
                    $parentStoreInfo->save();

                    // Check if the parent store has reached the points limit
                    if ($parentStoreInfo->points_limit >= 5000) {
                        $parentStoreInfo->status = 3; // Graduate
                        $parentStoreInfo->save();

                        // Update any stores that were invited by this parent
                        StoreInfo::where('invited_by', $parentId)
                                ->update(['invited_by' => null]);
                    }

                    // Recursively pass points to the grandparent store, etc.
                    $this->passPointsToParentStoreByStoreId($parentStoreInfo->id); // Pass the parent store ID
                }
            }
        }
    }



    protected function passPointsToParentStore($userId)
    {
        // get  inviter of the current user
        $inviterStoreInfo = StoreInfo::where('user_id', $userId)->first();
        $monitoring = WeeklyDashboardMonitoring::where('id', 2)->first(); //daily

        if ($inviterStoreInfo) {
            $parentId = $inviterStoreInfo->invited_by;

            if ($parentId) {
                // get the par ent's store info
                $parentStoreInfo = StoreInfo::where('user_id', $parentId)->first();

                if ($parentStoreInfo) {
                    // pass points to the parent store
                    $parentStoreInfo->points += 10;
                    $parentStoreInfo->points_limit += 10;
                    $monitoring->members_commission += 10;
                    $parentStoreInfo->save();
                    $monitoring->save();

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
        $user->load(['inviteCode']);
        // Get ancestors (all users who invited the current user)
        $ancestors = $this->getAncestors($user);

        // Get descendants (all users invited by the current user and their invitees)
        $descendants = $this->getDescendants($user);

        $avgPoints = $this->avgPoints($user->storeInfo);

        return response()->json([
            'user' => $user,
            'points' => $avgPoints,
            'ancestors' => $ancestors,
            'descendants' => $descendants,
        ]);
    }
    public function avgPoints(StoreInfo $store){
        $startDate = $store->created_at;
        $currentDate = Carbon::now();

        $hoursDifference = $startDate->diffInHours($currentDate);
        $daysDifference = round($hoursDifference / 24);

        if ($daysDifference == 0) {
            $daysDifference = 1;
        }

        $daily = $store->points_limit/$daysDifference;
        $weekly = $daily * min($daysDifference, 7);
        $status = $store->status === 1 ? 'STORE OPEN': 'STORE CLOSE';
        return [
            'daily' => $daily,
            'weekly' => $weekly,
            'status' => $status
        ];
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

        // $invitedUsers = InvitedUser::where('user_id', $user->id)->with(['invited.storeInfo'])->get();

        // foreach ($invitedUsers as $invitedUser) {
        //     $invited = User::find($invitedUser->invited_user);
        //     if ($invited) {
        //         $descendants[] = $invited;

        //         // Recursively get invitees of the invited user
        //         $childDescendants = $this->getDescendants($invited);
        //         $descendants = array_merge($descendants, $childDescendants);
        //     }
        // }

        $invitedUsers = InvitedUser::where('user_id', $user->id)
                        ->with(['invited.storeInfo'])
                        ->get();

        foreach ($invitedUsers as $invitedUser) {
            // Since 'invited' is eager loaded, use it directly from $invitedUser
            $invited = $invitedUser->invited; //storeInfo loaded
            $invited->status = $invited->storeInfo->status === 2 ? 'OPEN' : 'CLOSE';
            $invited->noInvited = $invited->storeInfo->status ;
            if ($invited) {
                $descendants[] = $invited;
                // Recursively get invitees of the invited user
                $childDescendants = $this->getDescendants($invited);
                $descendants = array_merge($descendants, $childDescendants);
            }
        }

        return $descendants;
    }

    public function getUserWithInvites($id): JsonResponse
    {
        try {
            // Eager load user_invitee and their invited users
            $user = User::with(['user_invitee.invited.user_invitee.invited'])->findOrFail($id);
            // Return a JsonResponse with the UserResource wrapped in it
            return response()->json(new UserResource($user));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found.'], 404);
        } catch (\Exception $e) {
            // Log the error message for debugging
            // \Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        // Validate the email input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch the user by email
        $user = User::where('email', $request->email)->get();

        if (!$user) {
            return response()->json(['errors' => ['email' =>'User not found']], 404);
        }

        if ($user->count() === 0 ) {
            return response()->json(['errors' => ['email' => 'User Dont Exist.']], 404);
        }
        // Retrieve all stores for the user
        // $stores = $user->store_referrer()->get();
        $user->load(['storeInfo']);
        return response()->json([
            'users' => $user,
        ]);
    }

    public function createStore(Request $request, User $user)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            //**********************/
            // Di na kailangan this kasi pasa nalang natin yung user since maayos naman yung relationship ng  dtb natin
            // yung user kasi connected naman na siya sa store_info, invitation_code
            //**********************/
            // 'invitation_code' => 'nullable|string',
            // 'inviting_store_id' => 'nullable|exists:store_infos,id', // Store ID of the inviter's store
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Initialize inviter-related variables
        $invitedById = null;
        $invitingStoreId = $request->inviting_store_id;

        // Handle invitation code
        if ($request->filled('invitation_code')) {
            // $invitationCode = InvitationCode::where('code', $request->invitation_code)->first();
            // eto magiging ganito
            $invitationCode = $user->inviteCode->code;

            if (!$invitationCode) {
                return response()->json(['message' => 'Invalid invitation code'], 400);
            }

            if ($invitationCode->used_count >= 2) {
                return response()->json(['message' => 'Invitation code has already been used 2 times'], 400);
            }

            // Get inviter's user ID
            $invitedById = $invitationCode->user_id;

            // Increment invitation code usage
            $invitationCode->increment('used_count');
        }

        // Prepare default store info
        $defaultStoreInfo = [
            'user_id' => $user->id,
            'invited_by' => $invitedById,
            'invitation_code' => $request->invitation_code,
            'points' => 0,
            'points_limit' => 100,
            'unpaid' => true,
            'status' => false,
            'last_redeemed' => now()->subDays(30),
        ];

        // Create store info for the new user
        $store_info = StoreInfo::create($defaultStoreInfo);

        // If an invitation code was provided, record the invitation and update inviter's info
        if ($invitedById) {
            // Record the invitation (link inviter and invitee using the inviter's user ID)
            $this->recordInvitation($request->invitation_code, $user->id);

            // Update inviter's store info and increment the invited users count
            $this->updateStoreInviterInfo($invitedById, $invitingStoreId);

            // Pass points to all ancestors of the user who invited the current user
            $this->passPointsToFirstOpenedStore($invitedById);
        }

        // Generate and save the unique invitation code for the new user
        $newInvitationCode = $this->generateInvitationCodeForUser($user->id);

        return response()->json([
            'message' => 'Store created successfully for existing user.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ],
            'default_store_info' => $store_info,
            'invitation_code' => $newInvitationCode,
        ], 201);
    }

    protected function passPointsToFirstOpenedStore($userId)
    {
        $inviterStoreInfo = StoreInfo::where('user_id', $userId)->first();
        $monitoring = WeeklyDashboardMonitoring::where('id', 2)->first();

        if ($inviterStoreInfo) {
            $firstOpenedStore = StoreInfo::where('invited_by', $inviterStoreInfo->invited_by)
                ->orderBy('created_at', 'asc') // Get the earliest store opened
                ->first();

            if ($firstOpenedStore) {

                if($firstOpenedStore->daily_points_timestamp == Carbon::today() && $firstOpenedStore->is_reached){
                    return response()->json(['message'=>'Daily limit reached']);
                }

                $firstOpenedStore->points += 10;
                $firstOpenedStore->points_limit += 10;
                $firstOpenedStore->points_today += 10;
                $monitoring->members_commission += 10;

                $firstOpenedStore->daily_points_timestamp = Carbon::today();

                $firstOpenedStore->save();
                $monitoring->save();

                if($firstOpenedStore->points_today >= 500 ){
                    $firstOpenedStore->is_reached = 1;
                }

                if ($firstOpenedStore->points_limit >= 5000) {
                    $firstOpenedStore->status = 3; // graduate
                    $firstOpenedStore->save();

                    StoreInfo::where('invited_by', $firstOpenedStore->user_id)
                        ->update(['invited_by' => null]);
                }

                $this->passPointsToFirstOpenedStore($firstOpenedStore->user_id);
            }
        }
    }



    public function createStoreV2(Request $request, User $user){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($user->inviteCode->used_count >= 2) {
            return response()->json(['errors' => ['message' => 'Invitation code has already been used 2 times']], 400);
        }
        $user->inviteCode->increment('used_count');
        $counter = $user->inviteCode->used_count;

        $weeklyDashboard = WeeklyDashboardMonitoring::where('id', 2)->first();
        $weeklyDashboard->package_sold += 1;
        $weeklyDashboard->product_purchased += 500;
        $weeklyDashboard->company_revenue += 1500;
        $weeklyDashboard->save();

        if($counter >= 2){
            $user->storeInfo->status = 1;
            $user->storeInfo->save();
            $newSequence = Sequence::create([
                'current_user_id' => $user->id,
                'before_user_id' => $user->id,
            ]);
            $this->sequencePassing($newSequence);
        }

        $noOfStore = User::where('email', $user->email)->count();
        $data = [
                "first_name" => $user->first_name,
                "middle_name" => $user->middle_name,
                "last_name"=> $user->last_name,
                "username"=> $user->username,
                "store_no"=> $noOfStore,
                "email"=> $user->email,
                "password"=> ':L}:?{<{H:SPE',
                "password_confirmation"=> ':L}:?{<{H:SPE',
                "photo_id"=> "some-photo-id",
                "invitation_code"=> $user->inviteCode->code
        ];
        $newUser = User::create($data);

        $defaultStoreInfo = [
            'user_id' => $newUser->id,
            'invited_by' => $user->id,
            'invitation_code' => $user->inviteCode->code,
            'points' => 0,
            'points_limit' => 100,
            'unpaid' => true,
            'status' => false,
            'last_redeemed' => now()->subDays(30),
        ];

        $newStore = StoreInfo::create($defaultStoreInfo);

        $this->generateInvitationCodeForUser($newUser->id);

        $this->recordInvitation($user->inviteCode->code, $newUser->id);
        $this->updateStoreInviterInfo($user->id, $newStore->id);

        $this->realtimeEvent();
        return response()->json(['message' => 'Success']);
    }

    public function recursiveParentPointPassing(InvitationCode $invitationCode){
        if($invitationCode->user->storeInfo->invitation_code !== null){
            $invTable = InvitationCode::where('code', $invitationCode->user->storeInfo->invitation_code)->first();
            $this->recursiveParentPointPassing($invTable);
        }

        $invitedUser = InvitedUser::where('user_id', $invitationCode->user->id)->get();
        $storeAreOpen = $this->checkStatusOfStore($invitedUser);

        if($storeAreOpen){
            $weeklyDashboard = WeeklyDashboardMonitoring::first();
            $weeklyDashboard->company_revenue -= 10;
            $weeklyDashboard->members_commission += 10;
            $weeklyDashboard->save();

            $invitationCode->user->storeInfo->points += 10;
            $invitationCode->user->storeInfo->save();
        }
    }

    public function sequencePassing(Sequence $newSequence){
        $sequencesBefore = Sequence::where('id', '<', $newSequence->id)
                                ->whereHas('userBefore', function ($query) {
                                    $query->where('admin', false);
                                })
                                ->get();
        $dailyMonitoring = WeeklyDashboardMonitoring::where('id', 2)->first();
        $setting = Setting::where('id', 1)->first();

        $specialIsOpen = $setting->special_feature;
        foreach ($sequencesBefore as $sequence){
            if(!$specialIsOpen && $setting->level === $setting->level_counter){

                if($sequence->userBefore->storeInfo->daily_points_timestamp->isSameDay(now())  && $sequence->userBefore->storeInfo->is_reached)
                {
                    //do nothing
                    // return response()->json(['message'=>'Daily 500 points received']);
                } else
                {
                    $sequence->userBefore->storeInfo->points += 10;
                    $sequence->userBefore->storeInfo->points_today += 10;

                    $sequence->userBefore->storeInfo->daily_points_timestamp = now();

                    $sequence->userBefore->storeInfo->save();

                    if($sequence->userBefore->storeInfo->points_today >= 500 ){
                        $sequence->userBefore->storeInfo->is_reached = 1;
                        $sequence->userBefore->storeInfo->points_today = 0;
                        $sequence->userBefore->storeInfo->save();
                    }

                    $dailyMonitoring->members_commission += 10;
                    $dailyMonitoring->company_revenue -= 10;
                    $dailyMonitoring->save();
                    $setting->level_counter = 1;
                    $setting->save();
                }

            } else
            {
                if($setting->level !== $setting->level_counter)
                {
                    $setting->level_counter += 1;
                    $setting->save();
                }
            }

        }
    }

    public function checkStatusOfStore($invitedUser){
        if($invitedUser[0]->invited->storeInfo->status === 1 && $invitedUser[1]->invited->storeInfo->status === 1 ){
            return true;
        } else {
            return false;
        }
    }

    public function realtimeEvent(){

        $adminController = new AdminController;
        $isPointingSystemStopped = false;
        $threshold = 0.9; // threshold for example 90% ganon
        $totalMembers = $adminController->getTotalMembers();
        $newMembers = $adminController->getNewMembers();
        $dailyPackageSales = $adminController->getDailyPackageSales();
        $dailyProductPurchased = $adminController->getDailyProductPurchased();
        $openStores = $adminController->getOpenStores();
        $graduatedStores = $adminController->getGraduatedStores();
        $dailyMembersCommission = $adminController->getDailyMembersCommission();
        $dailyCompanyRevenue = $adminController->getDailyCompanyRevenue();
        $weeklySales = $adminController->getWeeklySales();
        $weeklyDashboard = $adminController->weeklyDashboard();

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
            'weeklyDashboard'=>$weeklyDashboard
        ];

        broadcast(new DashboardUpdated($dashboardData));

    }
}
