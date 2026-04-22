<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Champs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'role',
        'password',
        'is_active',
        'banned_at',
        'ban_reason',
        'plan',
        'plan_expires_at',
        'listings_quota',
        'max_images_per_ad',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'banned_at'         => 'datetime',
            'plan_expires_at'   => 'datetime',
        ];
    }

    public static function getPlanConfig(string $slug)
    {
        $plans = \Illuminate\Support\Facades\Cache::remember('subscription_plans_all', 60*60*24, function () {
            return \App\Models\SubscriptionPlan::all()->keyBy('slug');
        });

        return $plans->get($slug);
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    /** Annonces publiées par l'utilisateur */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /** Annonces actives de l'utilisateur */
    public function activeListings(): HasMany
    {
        return $this->hasMany(Listing::class)->where('status', 'active');
    }

    /** Favoris de l'utilisateur (pivot) */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /** Annonces favorites (relation many-to-many) */
    public function favoriteListings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps();
    }

    /** Messages reçus */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /** Messages envoyés */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // ─── Vérification des rôles ───────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isParticulier(): bool
    {
        return $this->role === 'particulier';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Avatar généré via initiales
        $initials = collect(explode(' ', $this->name))
            ->map(fn ($word) => strtoupper($word[0]))
            ->take(2)
            ->implode('');
        return "https://ui-avatars.com/api/?name={$initials}&background=C8963E&color=fff&size=128";
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'        => 'Administrateur',
            'agent'        => 'Agent immobilier',
            'particulier'  => 'Particulier',
            'client'       => 'Client',
            default        => $this->role,
        };
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeBanned($query)
    {
        return $query->whereNotNull('banned_at');
    }

    public function scopeNotBanned($query)
    {
        return $query->whereNull('banned_at');
    }

    // ─── Ban Management ───────────────────────────────────────────────────────

    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    public function ban(?string $reason = null): void
    {
        $this->update([
            'banned_at' => now(),
            'ban_reason' => $reason,
            'is_active' => false,
        ]);
    }

    public function unban(): void
    {
        $this->update([
            'banned_at' => null,
            'ban_reason' => null,
            'is_active' => true,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isBanned()) {
            return 'Banni';
        }
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->isBanned()) {
            return 'red';
        }
        return $this->is_active ? 'green' : 'gray';
    }

    // ─── Subscription Management ─────────────────────────────────────────────

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activeSubscription()
    {
        return $this->payments()
            ->where('status', 'completed')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    public function getPlanLabelAttribute(): string
    {
        return self::getPlanConfig($this->plan)?->name ?? ucfirst($this->plan);
    }

    public function getPlanPriceAttribute(): int
    {
        return self::getPlanConfig($this->plan)?->price ?? 0;
    }

    public function getPlanQuotaAttribute(): int
    {
        $planData = self::getPlanConfig($this->plan) ?? self::getPlanConfig('gratuit');
        $role = $this->role === 'agent' ? 'agent' : 'particulier';
        if (!$planData) return 2;
        return $role === 'agent' ? $planData->max_ads_agent : $planData->max_ads_particulier;
    }

    public function getMaxImagesPerAdAttribute(): int
    {
        $planData = self::getPlanConfig($this->plan) ?? self::getPlanConfig('gratuit');
        $role = $this->role === 'agent' ? 'agent' : 'particulier';
        if (!$planData) return 5;
        return $role === 'agent' ? $planData->max_images_agent : $planData->max_images_particulier;
    }

    public function getPriorityLevelAttribute(): int
    {
        return self::getPlanConfig($this->plan)?->priority_level ?? 0;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->plan !== 'gratuit' 
            && $this->plan_expires_at 
            && $this->plan_expires_at->isFuture();
    }

    public function isPlanExpired(): bool
    {
        return $this->plan !== 'gratuit' 
            && $this->plan_expires_at 
            && $this->plan_expires_at->isPast();
    }

    public function getRemainingDaysAttribute(): int
    {
        if (!$this->plan_expires_at) {
            return 0;
        }
        return max(0, now()->diffInDays($this->plan_expires_at, false));
    }

    public function getListingDurationDaysAttribute(): int
    {
        return self::getPlanConfig($this->plan)?->listing_duration_days ?? 7;
    }

    public function getSponsoredListingDurationDaysAttribute(): int
    {
        return self::getPlanConfig($this->plan)?->sponsored_listing_duration_days ?? 0;
    }

    public function canCreateSponsoredListing(): bool
    {
        return $this->hasActiveSubscription()
            && (self::getPlanConfig($this->plan)?->can_create_sponsored_listing ?? false)
            && $this->sponsored_listing_duration_days > 0;
    }

    public function getPreferredSponsorshipTypeAttribute(): string
    {
        return match ($this->plan) {
            'agence' => 'premium_plus',
            'pro' => 'premium',
            'starter' => 'basic',
            default => 'basic',
        };
    }

    public function canCreateListing(): bool
    {
        $usedThisMonth = $this->listings()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $usedThisMonth < $this->plan_quota;
    }

    public function refreshListingDurationsForCurrentPlan(): void
    {
        $listingDurationDays = $this->listing_duration_days;

        $this->listings()
            ->whereIn('status', ['pending', 'active'])
            ->get()
            ->each(function (Listing $listing) use ($listingDurationDays) {
                $baseDate = $listing->expires_at && $listing->expires_at->isFuture()
                    ? $listing->expires_at->copy()
                    : now();

                $listing->update([
                    'expires_at' => $baseDate->addDays($listingDurationDays),
                ]);
            });
    }

    public function upgradeToPlan(string $plan, int $durationDays = 30): void
    {
        $planData = self::getPlanConfig($plan);

        if (!$planData) {
            return;
        }

        $role = $this->role === 'agent' ? 'agent' : 'particulier';

        $this->update([
            'plan' => $plan,
            'plan_expires_at' => now()->addDays($durationDays),
            'listings_quota' => $role === 'agent' ? $planData->max_ads_agent : $planData->max_ads_particulier,
            'max_images_per_ad' => $role === 'agent' ? $planData->max_images_agent : $planData->max_images_particulier,
        ]);

        $this->refresh()->refreshListingDurationsForCurrentPlan();
    }

    public function downgradeToFree(): void
    {
        $role = $this->role === 'agent' ? 'agent' : 'particulier';
        $freePlan = self::getPlanConfig('gratuit');
        
        $freeQuota = $freePlan ? ($role === 'agent' ? $freePlan->max_ads_agent : $freePlan->max_ads_particulier) : 2;
        $freeImages = $freePlan ? ($role === 'agent' ? $freePlan->max_images_agent : $freePlan->max_images_particulier) : 5;

        $this->update([
            'plan' => 'gratuit',
            'plan_expires_at' => null,
            'listings_quota' => $freeQuota,
            'max_images_per_ad' => $freeImages,
        ]);
    }
}
