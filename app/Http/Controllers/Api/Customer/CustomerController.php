<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController
{

    public function login(Request $request)
    {

      $fields = $request->validate([
        'name' => 'required|string',
        'password' => 'required|string',
      ]);

      $student = Customer::where('name', $fields['name'])->first();
      if (!$student || !Hash::check($fields['password'], $student['password'])) {
        return response([
          'error' => 'name or password is incorrect'
        ], 500);
      } else {

        $token = $student->createToken($request['name'], ['student'])->plainTextToken;

        $response = [
          'message' => "You have been logged in successfully",
          'student' => $student,
          'student_token' => $token
        ];

        return response($response, 201);
      }
    }


    public function index(){
        $customer = Auth::user();
        return response()->json([
            'data' => $customer,
            'message' => 'customer data retrieved successfully',
        ]);
    }

}
