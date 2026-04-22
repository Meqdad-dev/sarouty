<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'listing_id', 'user_id', 'reporter_email',
        'reason', 'description', 'status',
    ];

    const REASONS = [
        'frauduleux'           => 'Annonce frauduleuse',
        'doublon'              => 'Doublon',
        'prix_incorrect'       => 'Prix incorrect',
        'images_incorrectes'   => 'Images incorrectes',
        'contenu_inapproprie'  => 'Contenu inapproprié',
        'autre'                => 'Autre',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
