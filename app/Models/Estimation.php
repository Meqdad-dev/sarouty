<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    protected $fillable = [
        'property_type', 'transaction_type', 'city',
        'surface', 'bedrooms', 'bathrooms', 'floor', 'construction_year', 'condition',
        'has_garage', 'garage_places',
        'has_garden', 'garden_surface',
        'has_terrace', 'terrace_surface',
        'has_pool', 'has_elevator', 'has_parking', 'is_furnished', 'has_security',
        'estimated_min', 'estimated_mid', 'estimated_max', 'price_per_sqm',
        'user_type', 'wants_professional_help', 'is_owner', 'timeline',
        'contact_name', 'contact_email', 'contact_phone',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'surface'                  => 'decimal:2',
            'garden_surface'           => 'decimal:2',
            'terrace_surface'          => 'decimal:2',
            'estimated_min'            => 'decimal:2',
            'estimated_mid'            => 'decimal:2',
            'estimated_max'            => 'decimal:2',
            'price_per_sqm'            => 'decimal:2',
            'has_garage'               => 'boolean',
            'has_garden'               => 'boolean',
            'has_terrace'              => 'boolean',
            'has_pool'                 => 'boolean',
            'has_elevator'             => 'boolean',
            'has_parking'              => 'boolean',
            'is_furnished'             => 'boolean',
            'has_security'             => 'boolean',
            'wants_professional_help'  => 'boolean',
            'is_owner'                 => 'boolean',
        ];
    }
}
