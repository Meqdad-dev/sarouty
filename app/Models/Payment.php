<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const STATUSES = [
        'pending' => 'En attente',
        'completed' => 'Complété',
        'failed' => 'Échoué',
        'refunded' => 'Remboursé',
    ];

    const PLANS = [
        'gratuit' => ['name' => 'Gratuit', 'price' => 0],
        'starter' => ['name' => 'Starter', 'price' => 99],
        'pro' => ['name' => 'Pro', 'price' => 249],
        'agence' => ['name' => 'Agence', 'price' => 499],
    ];

    protected $fillable = [
        'user_id',
        'stripe_session_id',
        'stripe_payment_intent',
        'plan',
        'amount',
        'currency',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'currency' => 'MAD',
        'status' => 'pending',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'completed')
            ->where('expires_at', '>', now());
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getPlanLabelAttribute(): string
    {
        return self::PLANS[$this->plan]['name'] ?? ucfirst($this->plan);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'emerald',
            'pending' => 'amber',
            'failed' => 'red',
            'refunded' => 'purple',
            default => 'gray',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' ' . strtoupper($this->currency ?? 'MAD');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'completed' 
            && $this->expires_at 
            && $this->expires_at->isFuture();
    }

    public function getRemainingDaysAttribute(): int
    {
        if (!$this->expires_at || $this->status !== 'completed') {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }
}
