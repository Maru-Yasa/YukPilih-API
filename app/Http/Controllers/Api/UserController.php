<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;

class UserController extends Controller
{
    public function create(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username' => 'required|min:3|unique:users,username|alpha_dash',
            'password' => 'required|min:3',
            'role' => 'required',
            'devision_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => $validator->errors()
            ], 422);
        }
        $req['password'] = Hash::make($req->password);
        $user = User::create($req->all());
        return response()->json([
          'status' => 'success',
          'data' => $user,
          'msg' => 'Success add new user'  
        ], 200);
    }

    public function getAll(Request $req)
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => $users,
            'msg' => 'Success fetching all users'
        ], 200);
    }

    public function getById(Request $req, $user_id)
    {
        try {
            $user = User::all()->where('id', $user_id)->firstOrFail();
            return response()->json([
                'status' => 'success',
                'data' => $user,
                'msg' => 'Success fetching user data'
            ], 200);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'User not found'
            ], 404);
        }
    }

    public function update(Request $req, $user_id)
    {
        /* 
            TODO:
                1.make validation <sudah>
    
        */
        $validator = Validator::make($req->all(), [
            'username' => 'alpha_dash|unique:users,username|min:3',
            'password' => 'min:3',
        ]);
        
        try {
            $user = User::all()->where('id', $user_id)->firstOrFail();
            if ($req->password) {
                $req['password'] = Hash::make($req->password);
            }
            $user->update($req->all());
            return response()->json([
                'status' => 'success',
                'data' => $user,
                'msg' => 'Success fetching user data'
            ], 200);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'User not found'
            ], 404);
        } 

    }

    public function delete(Request $req, $user_id)
    {
        try {
            $user = User::all()->where('id', $user_id)->firstOrFail();
            $user->delete();
            return response()->json([
                'status' => 'success',
                'data' => $user,
                'msg' => 'Success deleting user'
            ], 200);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'User not found'
            ], 404);
        }
    }
}
