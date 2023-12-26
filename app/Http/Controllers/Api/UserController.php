<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get the user based on the provided token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserByToken(Request $request)
    {
        try {
            // Get the user based on the provided token
            $user = auth()->guard('api')->user();

            // Check if the user exists
            if ($user) {
                return response()->json([
                    'success' => true,
                    'user'    => $user,
                ], 200);
            } else {
                // User not found
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }
        } catch (\Exception $e) {
            // Error occurred while retrieving the user
            return response()->json([
                'success' => false,
                'message' => 'Error getting user',
            ], 500);
        }
    }
}
