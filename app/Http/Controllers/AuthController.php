<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService){
        $this->loginService = $loginService;
    }
    public function login(LoginRequest $request){
        $user = $request->validated();
        return $this->loginService->checkCredential($user);
    }
    
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'You are logged out'
        ]);
    }
}
