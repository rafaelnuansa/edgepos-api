<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomTokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('X-Edge-Token'); // Replace with your custom header name

        if ($token && $this->isValidCustomToken($token)) {
            return $next($request);
        }

        return response()->json(['error' => 'Invalid custom token'], 401);
    }

    private function isValidCustomToken($token)
    {
        // Implement your custom token validation logic here
        // For example, check if the token exists in a database table
        return User::where('token', $token)->exists();
    }
}
