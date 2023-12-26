<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Revoke the current user's token
        auth()->guard('api')->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
    }
}
