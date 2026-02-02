<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle($request, Closure $next)
    {
        // Laravel secara otomatis mengambil user berdasarkan Token Passport
        if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Hanya admin yang diperbolehkan.'], 403);
        }
        return $next($request);
    }
} 