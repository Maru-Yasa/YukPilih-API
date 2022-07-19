<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Devision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;

class DevisionController extends Controller
{
    public function create(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => $validator->errors()
            ], 422);
        }

        $devision = Devision::create($req->all());
        return response()->json([
            'status' => 'success',
            'data' => $devision,
            'msg' => 'Success create devision'
        ]);
    }

    public function getAll(Request $req)
    {
        $devisions = Devision::all();
        return response()->json([
            'status' => 'success',
            'data' => $devisions,
            'msg' => 'Success fething devision'
        ]);
    }

    public function getById(Request $req, $devision_id)
    {
        try {
            $devision = Devision::all()->where('id', $devision_id)->firstOrFail();
            return response()->json([
                'status' => 'success',
                'data' => $devision,
                'msg' => 'Success fetching devision'
            ]);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'devision not found'
            ], 404);
        }
    }

    public function update(Request $req, $devision_id)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => $validator->errors()
            ], 422);
        }

        try {
            $devision = Devision::all()->where('id', $devision_id)->firstOrFail();
            $devision->update($req->all());
            return response()->json([
                'status' => 'success',
                'data' => $devision,
                'msg' => 'Success update devision'
            ]);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'devision not found'
            ], 404);
        }
    }

    public function delete(request $req, $devision_id)
    {
        try {
            $devision = Devision::all()->where('id', $devision_id)->firstOrFail();
            $devision->delete();
            return response()->json([
                'status' => 'success',
                'data' => [],
                'msg' => 'Success delete devision'
            ]);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'devision not found'
            ], 404);
        }
    }

}
