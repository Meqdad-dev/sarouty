<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'listing_id', 'sender_id', 'receiver_id',
        'sender_name', 'sender_email', 'sender_phone', 'message', 
        'is_read', 'read_at', 'subject', 'replied_to_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function repliedTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'replied_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'replied_to_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeReceivedBy($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    public function scopeForListing($query, $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function isFromOwner(): bool
    {
        return $this->sender_id && $this->listing && $this->sender_id === $this->listing->user_id;
    }

    public function isToOwner(): bool
    {
        return $this->receiver_id && $this->listing && $this->receiver_id === $this->listing->user_id;
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getExcerptAttribute(): string
    {
        return \Str::limit(strip_tags($this->message), 100);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIsReplyAttribute(): bool
    {
        return !is_null($this->replied_to_id);
    }
}
