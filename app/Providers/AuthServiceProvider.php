<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\ClientRepository;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Note: Passport routes are now loaded via the package's route file
        // (Passport v12+). Calling Passport::routes() is deprecated/removed
        // and causes a fatal error. Routes will be available after
        // running `php artisan passport:install` which also creates the
        // necessary clients and keys.

        // Ensure a Passport personal access client exists to avoid runtime exceptions
        try {
            if (class_exists(Passport::class)) {
                $personal = Passport::personalAccessClient();

                if (! $personal->exists()) {
                    $repo = new ClientRepository();
                    $repo->createPersonalAccessClient(
                        null,
                        config('app.name') . ' Personal Access Client',
                        'http://localhost'
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Passport personal access client check failed: ' . $e->getMessage());
        }
    }
}
