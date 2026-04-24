<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\MediaStorageService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingImage extends Model
{
    protected $fillable = ['listing_id', 'path', 'alt', 'order'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function getUrlAttribute(): string
    {
        return app(MediaStorageService::class)->url($this->path) ?? '';
    }
}
