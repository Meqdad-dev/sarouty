<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SubscriptionPlan extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'slug', 'name', 'price', 'duration_days', 'priority_level',
        'listing_duration_days', 'sponsored_listing_duration_days',
        'can_create_sponsored_listing', 'max_images_particulier',
        'max_images_agent', 'max_ads_particulier', 'max_ads_agent',
        'features', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'can_create_sponsored_listing' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get and set the features attribute safely.
     * Handles cases where JSON might be double encoded.
     */
    protected function features(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    // Handle potential double encoding from old seeders
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }
                    return is_array($decoded) ? $decoded : [];
                }
                return is_array($value) ? $value : [];
            },
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }
}
