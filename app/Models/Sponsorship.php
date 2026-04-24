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

    public function syncListingState(): void
    {
        $listing = $this->listing;

        if (!$listing) {
            return;
        }

        if ($this->status === 'active') {
            $expiresAt = $this->expires_at ?: now()->addDays($this->duration_days ?: (self::TYPES[$this->type]['days'] ?? 7));

            $listing->update([
                'is_sponsored' => true,
                'sponsored_until' => $expiresAt,
            ]);

            return;
        }

        if (in_array($this->status, ['pending', 'paused'], true)) {
            $listing->update([
                'is_sponsored' => true,
                'sponsored_until' => $this->expires_at,
            ]);

            return;
        }

        $listing->deactivateSponsorship();
    }

    public function forcePending(): void
    {
        $this->update([
            'status' => 'pending',
            'starts_at' => null,
        ]);

        $this->syncListingState();
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($this->duration_days ?: (self::TYPES[$this->type]['days'] ?? 7)),
        ]);

        $this->syncListingState();
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
        $this->syncListingState();
    }

    public function resume(): void
    {
        $payload = ['status' => 'active'];

        if (!$this->expires_at || $this->expires_at->isPast()) {
            $payload['starts_at'] = now();
            $payload['expires_at'] = now()->addDays($this->duration_days ?: (self::TYPES[$this->type]['days'] ?? 7));
        }

        $this->update($payload);
        $this->syncListingState();
    }

    public function cancel(?string $reason = null): void
    {
        $payload = ['status' => 'cancelled'];

        if ($reason) {
            $payload['admin_notes'] = trim(($this->admin_notes ? $this->admin_notes . "\n" : '') . $reason);
        }

        $this->update($payload);
        $this->syncListingState();
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'expired']);
        $this->syncListingState();
    }

    public function syncFromListingStatus(?string $listingStatus = null): void
    {
        $status = $listingStatus ?? $this->listing?->status;

        if (!$status) {
            return;
        }

        if ($status === 'active') {
            if ($this->status === 'pending') {
                $this->activate();
            } elseif ($this->status === 'paused') {
                $this->resume();
            } elseif ($this->status === 'active') {
                $this->syncListingState();
            }

            return;
        }

        if ($status === 'pending') {
            $this->forcePending();
            return;
        }

        if ($status === 'rejected') {
            $this->cancel('Annonce refusée depuis la modération.');
            return;
        }

        if (in_array($status, ['sold', 'rented'], true)) {
            $this->cancel('Annonce clôturée depuis la gestion des annonces.');
        }
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
