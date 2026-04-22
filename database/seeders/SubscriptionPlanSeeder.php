<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'gratuit',
                'name' => 'Gratuit',
                'price' => 0,
                'duration_days' => null,
                'priority_level' => 0,
                'listing_duration_days' => 7,
                'sponsored_listing_duration_days' => 0,
                'can_create_sponsored_listing' => false,
                'max_images_particulier' => 5,
                'max_images_agent' => 10,
                'max_ads_particulier' => 2,
                'max_ads_agent' => 5,
                'features' => [
                    '2 annonces (particulier) / 5 (agent)',
                    '5 à 10 photos par annonce',
                    'Visibilité standard',
                    'Support email',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'price' => 149,
                'duration_days' => 30,
                'priority_level' => 2,
                'listing_duration_days' => 30,
                'sponsored_listing_duration_days' => 7,
                'can_create_sponsored_listing' => true,
                'max_images_particulier' => 10,
                'max_images_agent' => 15,
                'max_ads_particulier' => 10,
                'max_ads_agent' => 15,
                'features' => [
                    '10 annonces (particulier) / 15 (agent)',
                    '10 à 15 photos par annonce',
                    'Visibilité prioritaire',
                    'Support prioritaire',
                    'Mise en avant des annonces',
                    'Statistiques détaillées',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro',
                'price' => 349,
                'duration_days' => 30,
                'priority_level' => 5,
                'listing_duration_days' => 60,
                'sponsored_listing_duration_days' => 14,
                'can_create_sponsored_listing' => true,
                'max_images_particulier' => 15,
                'max_images_agent' => 20,
                'max_ads_particulier' => 20,
                'max_ads_agent' => 30,
                'features' => [
                    '20 annonces (particulier) / 30 (agent)',
                    '15 à 20 photos par annonce',
                    'Visibilité premium',
                    'Support téléphonique dédié',
                    'Mise en avant incluse',
                    'Statistiques avancées',
                    'Badge Agent vérifié',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'agence',
                'name' => 'Agence',
                'price' => 799,
                'duration_days' => 30,
                'priority_level' => 8,
                'listing_duration_days' => 90,
                'sponsored_listing_duration_days' => 30,
                'can_create_sponsored_listing' => true,
                'max_images_particulier' => 20,
                'max_images_agent' => 30,
                'max_ads_particulier' => 100,
                'max_ads_agent' => 999,
                'features' => [
                    'Annonces illimitées',
                    '20 à 30 photos par annonce',
                    'Visibilité premium maximale',
                    'Account manager dédié',
                    'Mise en avant prioritaire',
                    'Rapports et analytiques avancés',
                    'Badge Agence certifiée',
                    'Accès API & export CSV',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
