<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'payment_id',
        'type',
        'status',
        'starts_at',
        'expires_at',
        'amount',
        'currency',
        'duration_days',
        'impressions',
        'clicks',
        'contacts',
        'admin_notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $attributes = [
        'currency' => 'MAD',
        'impressions' => 0,
        'clicks' => 0,
        'contacts' => 0,
    ];

    // ─── Constants ───────────────────────────────────────────────────────────

    const TYPES = [
        'basic' => [
            'label' => 'Basique',
            'days' => 7,
            'price' => 99,
        ],
        'premium' => [
            'label' => 'Premium',
            'days' => 14,
            'price' => 199,
        ],
        'premium_plus' => [
            'label' => 'Premium+',
            'days' => 30,
            'price' => 349,
        ],
    ];

    const STATUSES = [
        'pending' => 'En attente',
        'active' => 'Active',
        'paused' => 'En pause',
        'expired' => 'Expirée',
        'cancelled' => 'Annulée',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->where('expires_at', '<=', now());
            });
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'emerald',
            'pending' => 'amber',
            'paused' => 'blue',
            'expired' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' ' . $this->currency;
    }

    public function getRemainingDaysAttribute(): int
    {
        if (!$this->expires_at || $this->status !== 'active') {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) {
            return 0;
        }
        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at <= now()
            && $this->expires_at > now();
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($this->duration_days),
        ]);
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function incrementImpressions(): void
    {
        $this->increment('impressions');
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    public function incrementContacts(): void
    {
        $this->increment('contacts');
    }
}
