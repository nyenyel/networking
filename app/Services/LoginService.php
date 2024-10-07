<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginService {
    public function checkCredential($data){
        $user = User::where('email', $data['email'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            return response()->json(['message' => 'Invalid Credential']);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => UserResource::make($user),
            'token' => $token
        ]);
    }
}