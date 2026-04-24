<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SubscriptionPlan extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public const THEME_PRESETS = [
        'gray' => [
            'name' => 'Gris perle',
            'hex' => '#94A3B8',
            'soft' => '#F8FAFC',
            'border' => '#CBD5E1',
            'text' => '#475569',
            'button' => '#334155',
            'button_hover' => '#1E293B',
            'glow' => 'rgba(148, 163, 184, 0.28)',
        ],
        'gold' => [
            'name' => 'Or premium',
            'hex' => '#C8963E',
            'soft' => '#FFF8EC',
            'border' => '#E8C47A',
            'text' => '#9A6A1F',
            'button' => '#C8963E',
            'button_hover' => '#B78227',
            'glow' => 'rgba(200, 150, 62, 0.24)',
        ],
        'ink' => [
            'name' => 'Noir encre',
            'hex' => '#1A1410',
            'soft' => '#F5F2EF',
            'border' => '#D8CEC4',
            'text' => '#1A1410',
            'button' => '#1A1410',
            'button_hover' => '#2D2520',
            'glow' => 'rgba(26, 20, 16, 0.18)',
        ],
        'terracotta' => [
            'name' => 'Terracotta',
            'hex' => '#C96F4A',
            'soft' => '#FFF3EE',
            'border' => '#F0B39A',
            'text' => '#A94E29',
            'button' => '#C96F4A',
            'button_hover' => '#B85B36',
            'glow' => 'rgba(201, 111, 74, 0.22)',
        ],
        'emerald' => [
            'name' => 'Émeraude',
            'hex' => '#0F9F78',
            'soft' => '#ECFDF5',
            'border' => '#6EE7B7',
            'text' => '#047857',
            'button' => '#0F9F78',
            'button_hover' => '#0B7F60',
            'glow' => 'rgba(15, 159, 120, 0.22)',
        ],
        'rose' => [
            'name' => 'Rose signature',
            'hex' => '#E05A8D',
            'soft' => '#FFF1F7',
            'border' => '#F4A7C6',
            'text' => '#BE2F67',
            'button' => '#E05A8D',
            'button_hover' => '#C94678',
            'glow' => 'rgba(224, 90, 141, 0.22)',
        ],
    ];

    protected $fillable = [
        'slug', 'name', 'price', 'duration_days', 'priority_level',
        'listing_duration_days', 'sponsored_listing_duration_days',
        'can_create_sponsored_listing', 'max_images_particulier',
        'max_images_agent', 'max_ads_particulier', 'max_ads_agent',
        'features', 'is_active', 'theme_color',
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

    public static function defaultThemeForSlug(?string $slug): string
    {
        return match ($slug) {
            'gratuit' => 'gray',
            'starter' => 'gold',
            'pro' => 'ink',
            'agence' => 'terracotta',
            default => 'emerald',
        };
    }

    public function getThemeKeyAttribute(): string
    {
        $key = $this->theme_color ?: static::defaultThemeForSlug($this->slug);

        return array_key_exists($key, static::THEME_PRESETS)
            ? $key
            : static::defaultThemeForSlug($this->slug);
    }

    public function getThemePresetAttribute(): array
    {
        return static::THEME_PRESETS[$this->theme_key] ?? static::THEME_PRESETS['gray'];
    }

    public function getThemeHexAttribute(): string
    {
        return $this->theme_preset['hex'];
    }

    public function getThemeNameAttribute(): string
    {
        return $this->theme_preset['name'];
    }
}
