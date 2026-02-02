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
    // Jangan redirect jika request ke API, return null agar error 401
    if ($request->expectsJson() || $request->is('api/*')) {
        return null;
    }
    
    return route('login');
}
}
