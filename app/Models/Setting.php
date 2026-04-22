<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    // ─── Groups ───────────────────────────────────────────────────────────────

    const GROUPS = [
        'general' => 'Paramètres généraux',
        'contact' => 'Contact',
        'social' => 'Réseaux sociaux',
        'listing' => 'Annonces',
    ];

    // ─── Static Helper Methods ────────────────────────────────────────────────

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
            Cache::forget("setting.{$key}");
            Cache::forget('settings.all');
        }
    }

    /**
     * Get all settings grouped by group.
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember('settings.all', 3600, function () {
            return static::all()->groupBy('group')->toArray();
        });
    }

    /**
     * Get public settings (for frontend).
     */
    public static function getPublic(): array
    {
        return Cache::remember('settings.public', 3600, function () {
            return static::where('is_public', true)
                ->get()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('settings.all');
        Cache::forget('settings.public');
        
        // Clear individual setting caches
        foreach (static::all() as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
    }

    /**
     * Cast value to proper type.
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => (bool) $value,
            'number' => is_numeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getGroupLabelAttribute(): string
    {
        return self::GROUPS[$this->group] ?? $this->group;
    }

    public function getTypedValueAttribute(): mixed
    {
        return static::castValue($this->value, $this->type);
    }
}
