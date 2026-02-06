<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccessTokenHeader
{
    /**
     * If request has ?access_token=... and no Authorization header, set it.
     */
    public function handle(Request $request, Closure $next)
    {
        $hasAuth = $request->headers->has('authorization') || $request->headers->has('Authorization');

        if (! $hasAuth && $request->query('access_token')) {
            $token = $request->query('access_token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
            // also set SERVER var so some servers/frameworks pick it up
            $serverKey = 'HTTP_AUTHORIZATION';
            if (! isset($_SERVER[$serverKey])) {
                $_SERVER[$serverKey] = 'Bearer ' . $token;
            }
        }

        return $next($request);
    }
}
