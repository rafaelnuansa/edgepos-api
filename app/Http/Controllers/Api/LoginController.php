<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        //if auth success
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api')->user(),
            'token'   => $token
        ], 200);
    }

    public function authCheck()
    {
        // Check if the user is authenticated
        if (auth()->guard('api')->check()) {
            // User is authenticated
            return response()->json([
                'success' => true,
                'user' => auth()->guard('api')->user(),
            ], 200);
        } else {
            // User is not authenticated
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. User is not authenticated.',
            ], 401);
        }
    }
}
