<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteUserRequest;
use App\Models\User;
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

        $defaultStoreInfo = [
            'user_id' => $user->id,
            // 'invited_by' => Auth::user()->id,
            'invited_by' => 1, //for API TESTING ONLY
            'points' => 0,
            'unpaid' => 1,
            'status' => 0,
        ];

        $store_info = StoreInfo::create($defaultStoreInfo);

        return response()->json([
            'message'=>'success',
            'user' => $user,
            'default_store_info' => $store_info
        ]);
    }
}
