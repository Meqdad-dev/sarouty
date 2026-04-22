<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'price_period', 'surface',
        'rooms', 'bathrooms', 'floor', 'transaction_type', 'property_type',
        'city', 'zone', 'address', 'latitude', 'longitude',
        'furnished', 'parking', 'elevator', 'pool', 'garden',
        'terrace', 'security', 'status', 'rejection_reason',
        'views', 'featured', 'thumbnail', 'priority',
        'is_sponsored', 'sponsored_until', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'surface'   => 'decimal:2',
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'furnished' => 'boolean',
            'parking'   => 'boolean',
            'elevator'  => 'boolean',
            'pool'      => 'boolean',
            'garden'    => 'boolean',
            'terrace'   => 'boolean',
            'security'  => 'boolean',
            'featured'  => 'boolean',
            'is_sponsored' => 'boolean',
            'sponsored_until' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ─── Constantes ───────────────────────────────────────────────────────────

    const TRANSACTION_TYPES = [
        'vente'    => 'Vente',
        'location' => 'Location longue durée',
        'neuf'     => 'Immobilier neuf',
        'vacances' => 'Location vacances',
    ];

    const PROPERTY_TYPES = [
        'appartement' => 'Appartement',
        'villa'       => 'Villa',
        'terrain'     => 'Terrain',
        'riad'        => 'Riad',
        'bureau'      => 'Bureau',
        'commerce'    => 'Commerce',
        'maison'      => 'Maison',
        'ferme'       => 'Ferme',
    ];

    const PRICE_PERIODS = [
        'jour'    => 'Par jour',
        'semaine' => 'Par semaine',
        'mois'    => 'Par mois',
        'annee'   => 'Par an',
    ];

    const CITIES = [
        'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
        'Agadir', 'Meknès', 'Oujda', 'Kénitra', 'Tétouan',
        'Salé', 'Mohammedia', 'El Jadida', 'Essaouira', 'Ifrane',
        'Béni Mellal', 'Nador', 'Settat', 'Safi', 'Errachidia',
    ];

    const STATUSES = [
        'pending'  => 'En attente',
        'active'   => 'Active',
        'rejected' => 'Refusée',
        'sold'     => 'Vendue',
        'rented'   => 'Louée',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('order');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function sponsorship(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Sponsorship::class)->latestOfMany();
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function activeSponsorship(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Sponsorship::class)->active();
    }

    public function aiModeration(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AiModeration::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($sub) {
                $sub->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true)->where('status', 'active');
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByProperty($query, string $type)
    {
        return $query->where('property_type', $type);
    }

    public function scopePriceRange($query, ?float $min, ?float $max)
    {
        if ($min) $query->where('price', '>=', $min);
        if ($max) $query->where('price', '<=', $max);
        return $query;
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('city', 'like', "%{$term}%")
              ->orWhere('zone', 'like', "%{$term}%");
        });
    }

    public function scopeHighPriority($query, int $minPriority = 5)
    {
        return $query->where('priority', '>=', $minPriority);
    }

    public function scopeOrderByPriority($query)
    {
        return $query->orderByDesc('priority');
    }

    public function scopeSponsored($query)
    {
        return $query->where('is_sponsored', true)
            ->where('sponsored_until', '>', now());
    }

    /**
     * Classement intelligent : agents premium sponsorises > agents sponsorises >
     * particuliers sponsorises > annonces normales, puis priorite, puis recence.
     */
    public function scopeRanked($query)
    {
        return $query
            ->leftJoin('users', 'listings.user_id', '=', 'users.id')
            ->select('listings.*')
            ->orderByRaw("
                CASE
                    WHEN listings.is_sponsored = 1 AND listings.sponsored_until > NOW() AND users.role = 'agent' AND users.plan IN ('pro','agence') THEN 1
                    WHEN listings.is_sponsored = 1 AND listings.sponsored_until > NOW() AND users.role = 'agent' THEN 2
                    WHEN listings.is_sponsored = 1 AND listings.sponsored_until > NOW() THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderByDesc('listings.priority')
            ->orderByDesc('listings.created_at');
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────────

    public function getFormattedPriceAttribute(): string
    {
        $suffix = '';
        if (in_array($this->transaction_type, ['location', 'vacances'])) {
            $suffix = $this->price_period ? ' / ' . ($this->price_period === 'annee' ? 'an' : $this->price_period) : '';
        }

        return number_format($this->price, 0, ',', ' ') . ' MAD' . $suffix;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return Str::startsWith($this->thumbnail, ['http://', 'https://'])
                ? $this->thumbnail
                : asset('storage/' . $this->thumbnail);
        }

        if ($this->images->isNotEmpty()) {
            $firstImage = $this->images->first()->path;
            return Str::startsWith($firstImage, ['http://', 'https://'])
                ? $firstImage
                : asset('storage/' . $firstImage);
        }

        return "data:image/svg+xml," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1A1410"/><stop offset="100%" stop-color="#3D6B20"/></linearGradient></defs><rect width="1200" height="800" fill="url(#g)"/><circle cx="980" cy="160" r="110" fill="#C8963E" fill-opacity="0.18"/><path d="M220 470l180-150 130 110 170-150 220 190v130H220z" fill="#ffffff" fill-opacity="0.12"/><text x="90" y="655" fill="#F8F3E8" font-family="Arial, sans-serif" font-size="58" font-weight="700">Sarouty</text><text x="90" y="715" fill="#E8D9C0" font-family="Arial, sans-serif" font-size="28">Annonce en attente de visuel</text></svg>');
    }

    public function getLocationLabelAttribute(): string
    {
        return collect([$this->address, $this->zone, $this->city])
            ->filter(fn ($part) => filled($part))
            ->implode(', ');
    }

    public function getGoogleMapsQueryAttribute(): ?string
    {
        $parts = array_values(array_filter([$this->address, $this->zone, $this->city, 'Maroc']));

        if (!empty($parts)) {
            return implode(', ', $parts);
        }

        if ($this->latitude && $this->longitude) {
            return $this->latitude . ',' . $this->longitude;
        }

        return null;
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        $query = $this->google_maps_query;

        if (!$query) {
            return null;
        }

        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($query);
    }

    public function getGoogleMapsEmbedUrlAttribute(): ?string
    {
        $query = $this->google_maps_query;

        if (!$query) {
            return null;
        }

        $center = ($this->latitude && $this->longitude)
            ? '&ll=' . $this->latitude . ',' . $this->longitude
            : '';

        return 'https://www.google.com/maps?q=' . urlencode($query) . $center . '&hl=fr&z=16&output=embed';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'green',
            'pending'  => 'yellow',
            'rejected' => 'red',
            'sold'     => 'blue',
            'rented'   => 'purple',
            default    => 'gray',
        };
    }

    public function getTransactionLabelAttribute(): string
    {
        return self::TRANSACTION_TYPES[$this->transaction_type] ?? $this->transaction_type;
    }

    public function getPropertyLabelAttribute(): string
    {
        return self::PROPERTY_TYPES[$this->property_type] ?? $this->property_type;
    }

    public function getPriorityLabelAttribute(): string
    {
        return match (true) {
            $this->priority >= 8 => 'Priorité maximale',
            $this->priority >= 5 => 'Priorité haute',
            $this->priority >= 3 => 'Priorité moyenne',
            $this->priority >= 1 => 'Priorité basse',
            default => 'Normale',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match (true) {
            $this->priority >= 8 => 'red',
            $this->priority >= 5 => 'orange',
            $this->priority >= 3 => 'yellow',
            $this->priority >= 1 => 'blue',
            default => 'gray',
        };
    }

    /**
     * Vérifie si l'annonce est dans les favoris d'un utilisateur.
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    /**
     * Incrémente le compteur de vues.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    // ─── Sponsorisation ───────────────────────────────────────────────────────

    /**
     * Verifie si l'annonce est actuellement sponsorisee.
     */
    public function isCurrentlySponsored(): bool
    {
        return $this->is_sponsored
            && $this->sponsored_until
            && $this->sponsored_until->isFuture();
    }

    /**
     * Active la sponsorisation pour un nombre de jours.
     */
    public function activateSponsorship(int $days, int $priorityBoost = 3): void
    {
        $this->update([
            'is_sponsored' => true,
            'sponsored_until' => now()->addDays($days),
            'priority' => min(10, $this->priority + $priorityBoost),
        ]);
    }

    /**
     * Desactive la sponsorisation.
     */
    public function deactivateSponsorship(): void
    {
        $this->update([
            'is_sponsored' => false,
            'sponsored_until' => null,
        ]);
    }

    /**
     * Jours restants de sponsorisation.
     */
    public function getSponsoredRemainingDaysAttribute(): int
    {
        if (!$this->sponsored_until || !$this->is_sponsored) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->sponsored_until, false));
    }
}
