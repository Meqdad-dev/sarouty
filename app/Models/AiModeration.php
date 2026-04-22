<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiModeration extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'approved',
        'risk_score',
        'flags',
        'reason',
        'model',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'flags' => 'array',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
