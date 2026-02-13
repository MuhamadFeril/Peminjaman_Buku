<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
      RateLimiter::for('api', function (Request $request) {
    // Whitelist localhost: Matikan jika ingin ngetes limit sendiri
    // if ($request->ip() === '127.0.0.1' || $request->ip() === '::1') {
    //     return Limit::none();
    // }

    $key = $request->user()?->id ?: $request->ip();
    $method = strtoupper($request->method());

    // 1. Ambil nama tabel/resource (buku, anggota, dll)
    $route = $request->route();
    $resource = 'default';
    if ($route) {
        $uri = method_exists($route, 'uri') ? $route->uri() : ($route->getAction('uri') ?? 'default');
        $resource = explode('/', ltrim($uri, 'api/'))[0] ?: 'default';
    }

    // 2. Bersihkan nama resource untuk jadi Key
    $matched = preg_replace('/[^a-z0-9]/', '', strtolower($resource));

    // 3. PAKSA LIMIT 5 UNTUK SEMUA METHOD (Termasuk GET)
    // Dengan menggabungkan $method, GET akan punya hitungan sendiri sebanyak 5 kali
    return Limit::perMinute(5)->by($key . $matched . $method)->response(function () use ($method, $matched) {
        return response()->json([
            'meta' => [
                'code' => 429,
                'status' => 'error',
                'message' => "Limit tercapai! Request $method pada $matched maksimal 5x per menit."
            ],
            'data' => null
        ], 429);
    });
});

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    } // Penutup boot
} // Penutup class