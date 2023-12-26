<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenValidationController extends Controller
{
    /**
     * Check the validity of the provided token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkToken(Request $request)
    {
        // Get the token from the request
        $token = $request->bearerToken();

        // Check if the token is valid
        try {
            $user = auth()->guard('api')->authenticate($token);

            // Token is valid, return success response
            return response()->json([
                'success' => true,
                'user'    => $user,
            ], 200);
        } catch (\Exception $e) {
            // Token is invalid or expired, return error response
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid or expired',
            ], 401);
        }
    }

    public function refreshToken(Request $request)
    {
        // Get the refresh token from the request
        $refreshToken = $request->bearerToken();

        // Attempt to refresh the token
        try {
            $newToken = Auth::guard('api')->refresh($refreshToken);

            // Token refreshed successfully, return the new token
            return response()->json([
                'success'   => true,
                'token'     => $newToken,
            ], 200);
        } catch (\Exception $e) {
            // Token refresh failed, return error response
            return response()->json([
                'success' => false,
                'message' => 'Unable to refresh token',
            ], 401);
        }
    }
}
