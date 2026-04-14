<?php

namespace Database\Seeders;

use App\Models\BillingPlan;
use Illuminate\Database\Seeder;

class BillingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Mesačný',
                'slug' => 'monthly',
                'price' => 39.00,
                'interval' => 'monthly',
                'max_webinars' => null,
                'max_registrants' => null,
                'features' => [
                    'Neobmedzené webináre',
                    'Neobmedzené smart videá',
                    'Neobmedzené registrácie',
                    'Email pripomienky',
                    'SMS pripomienky',
                    'Analytika',
                    'Webhooky',
                    'Tracking pixely',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Ročný',
                'slug' => 'yearly',
                'price' => 390.00,
                'interval' => 'yearly',
                'max_webinars' => null,
                'max_registrants' => null,
                'features' => [
                    'Všetko z Mesačného plánu',
                    'Zľava 17% (32,50 €/mes)',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Lifetime',
                'slug' => 'lifetime',
                'price' => 1170.00,
                'interval' => 'lifetime',
                'max_webinars' => null,
                'max_registrants' => null,
                'features' => [
                    'Všetko z Mesačného plánu',
                    'Jednorazová platba',
                    'Prístup navždy',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            BillingPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
