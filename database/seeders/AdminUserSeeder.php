<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'adminperpus90@gmail.com';

        if (User::where('email', $email)->exists()) {
            $this->command->info('Admin user already exists: ' . $email);
            return;
        }

        // Use ADMIN_PASSWORD from .env if provided, otherwise generate a random password.
        $envPassword = env('ADMIN_PASSWORD');
        $passwordToUse = $envPassword && strlen($envPassword) > 0 ? $envPassword : Str::random(16);

        if (User::where('email', $email)->exists()) {
            // If user exists and ADMIN_PASSWORD provided, update password. Otherwise leave as-is.
            $existing = User::where('email', $email)->first();
            if ($envPassword && strlen($envPassword) > 0) {
                $existing->password = Hash::make($envPassword);
                $existing->save();
                $this->command->info('Admin user already existed; password updated from ADMIN_PASSWORD for: ' . $email);
            } else {
                $this->command->info('Admin user already exists: ' . $email . ' (password unchanged)');
            }
            return;
        }

        // Create new admin user
        $user = User::create([
            'name' => 'adminbanget',
            'email' => $email,
            'password' => Hash::make($passwordToUse),
            'role' => 'admin',
        ]);

        if ($envPassword && strlen($envPassword) > 0) {
            $this->command->info('Admin user created: ' . $email . ' (password set from ADMIN_PASSWORD)');
        } else {
            $this->command->info('Admin user created: ' . $email . ' (password generated, not displayed)');
        }
    }
}
