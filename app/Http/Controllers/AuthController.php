<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function show($id)
    {
        try {
            $token = PersonalAccessToken::findOrFail($id);
            $username = User::findOrFail($token->tokenable_id);
            return response()->json([
                'id' => $token->id,
                'username' => $username->username,
                'expires' => $token->expires_at,
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Token not found'
            ], 500);
        }       
    }

    public function update($id)
    {
        try {
            $expired_date = Carbon::now()->addHour(3)->toDateTimeString(); 
            $PAT =  PersonalAccessToken::findOrFail($id);
            $PAT -> expires_at = $expired_date->toDateTimeString();
            $PAT -> save();
            return response()->json([
                'message' => 'Session extended',
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Token not found'
            ], 500);
        }
    }


    public function createUser(Request $request)
    {
        
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'username' => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['username', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Username & Password does not match with our record.',
                ], 401);
            }
            $user = User::where('username', $request->username)->first();
            $count = PersonalAccessToken::selectRaw('count(*) as total')
                        ->where('tokenable_id',$user->id)
                        ->first();
            $now = Carbon::now();
            if ($count->total >= 1) {
                $exp = PersonalAccessToken::selectRaw('expires_at, id')
                        ->where('tokenable_id',$user->id)
                        ->first();
                if ($exp->expires_at->lt($now)) {
                    $tok = PersonalAccessToken::findOrFail($exp->id);
                    $tok->delete();

                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have already login.',
                    ], 401);
                }
            }

            $expired_date = Carbon::now()->addHour(3)->toDateTimeString(); 
            $bearerAuth = $user->createToken("API TOKEN")->plainTextToken;
            $tokenID = PersonalAccessToken::all()->last()->id;
            $PAT =  PersonalAccessToken::findOrFail($tokenID);
            $PAT -> expires_at = $expired_date;
            $PAT -> save();

            return response()->json([
                'message' => 'Logged In',
                'token' => $bearerAuth,
                'tokenID' => $tokenID,
                                 
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request, $id) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => "Logout"
        ], 200);
    }
}