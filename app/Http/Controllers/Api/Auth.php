<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Auth extends Controller
{
    
    public function login(Request $req)
    {
    
        $validator = Validator::make($req->all(), [
            'username' => 'required|min:3',
            'password' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => "error",
                'data' => [],
                'msg' => $validator->errors() 
            ], 401);    
        }

        if(!FacadesAuth::attempt($req->only('username', 'password'))){
            return response()->json([
                'status' => 'unauthorized',
                'data' => [],
                'msg' => 'username or password invalid'
            ], 401);
        }

        $user = User::all()->where('username', $req->username)->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);

    }

    public function logout(Request $req)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'data' => [],
            'msg' => 'success logout'
        ]);
    }

    public function me(Request $req)
    {
        $user = auth()->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user
            ],
            'msg' => 'success fetching current user'
        ]);
    }

    public function resetPassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:3|different:old_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        if(!Hash::check($req->old_password, $user->password)){
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'old password not match'
            ], 422);
        }

        $user = User::all()->where('id', $user->id)->firstOrFail();
        $user->update([
            'password' => Hash::make($req->new_password)
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [],
            'msg' => 'success reset password'
        ], 200);
    }

}
