<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination views instead of Tailwind
        Paginator::useBootstrapFive();

        // Dynamically adjust URL scheme and session cookie settings so the app
        // works both on local (http://127.0.0.1) and on the dev tunnel (https).
        try {
            $host = request()->getHost();
        } catch (\Throwable $e) {
            $host = null;
        }

        if ($host && str_contains($host, 'devtunnels.ms')) {
            // Force https for dev tunnel host and ensure cookies are secure and
            // scoped to the tunnel host so the browser will send them.
            URL::forceScheme('https');
            config(['session.secure' => true]);
            config(['session.domain' => $host]);
            // Ensure generated URLs use this host
            $root = request()->getSchemeAndHttpHost();
            URL::forceRootUrl($root);
            config(['app.url' => $root]);
        } else {
            // Local development: do not force https and use a null domain so
            // the session cookie is available on localhost/127.0.0.1.
            config(['session.secure' => false]);
            config(['session.domain' => null]);
            // Ensure generated URLs use local host
            try {
                $root = request()->getSchemeAndHttpHost();
                URL::forceRootUrl($root);
                config(['app.url' => $root]);
            } catch (\Throwable $e) {
                // ignore when request not available (artisan commands)
            }
        }
    }
}
