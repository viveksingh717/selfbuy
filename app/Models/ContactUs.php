<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ContactUs extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact_us';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'is_starred',
        'admin_reply',
        'replied_at',
        'replied_by',
        'ip_address',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'replied_at' => 'datetime',
    ];

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /* ── Accessors ──────────────────────────────────────────── */

    /** First letter of the name, for the letter-avatar. */
    public function getInitialAttribute(): string
    {
        return Str::upper(Str::substr(trim($this->name), 0, 1) ?: '?');
    }

    /** Deterministic colour for the letter-avatar background. */
    public function getAvatarColorAttribute(): string
    {
        $palette = ['#5A67D8', '#38A169', '#DD6B20', '#E53E3E', '#319795', '#805AD5', '#D53F8C', '#3182CE'];

        return $palette[ord(Str::upper(Str::substr($this->name ?: 'A', 0, 1))) % count($palette)];
    }

    public function getIsRepliedAttribute(): bool
    {
        return !is_null($this->replied_at);
    }
}
