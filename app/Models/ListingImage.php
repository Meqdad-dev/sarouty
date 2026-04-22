<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ListingImage extends Model
{
    protected $fillable = ['listing_id', 'path', 'alt', 'order'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function getUrlAttribute(): string
    {
        return Str::startsWith($this->path, ['http://', 'https://'])
            ? $this->path
            : asset('storage/' . $this->path);
    }
}
