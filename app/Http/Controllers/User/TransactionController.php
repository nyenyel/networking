<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function transaction(Request $request){
        $user = Auth::user();
        if (!$user) {
            DB::rollBack();
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
        return response()->json($user->transactions);
    }
}
