<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController
{

    public function login(Request $request)
    {

        $fields = $request->validate(
            [
                'name' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'name.required' => 'name is required',
                'password.required' => 'Password is required',
            ]
        );

        $admin = Admin::where('name', $fields['name'])->first();

        if (!$admin || !Hash::check($fields['password'], $admin->password)) {
            return response([
                'error' => 'Username or Password wrong'
            ], 401);
        }

        $token = $admin->createToken($request['name'], ['admin'])->plainTextToken;

        $response = [
            'admin' => $admin,
            'admin_token' => $token
        ];

        return response($response, 201);
    }



    public function index(){
        $admin = Auth::user();
        return response()->json([
            'data' => $admin,
            'message' => 'Admin data retrieved successfully',
        ]);
    }

}
