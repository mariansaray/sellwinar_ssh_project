<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'sellwinar-platform'],
            [
                'name' => 'Sellwinar Platform',
                'plan' => 'lifetime',
                'subscription_status' => 'active',
                'settings' => [],
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@sellwinar.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Super Admin',
                'password' => bcrypt('admin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
                'api_key' => Str::random(64),
            ]
        );
    }
}
