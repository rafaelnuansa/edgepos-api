<?php
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('api/*')) {
            return route('auth.check'); // Change 'auth.check' to your actual route name
        }

        return $request->expectsJson() ? null : route('login');
    }
}
