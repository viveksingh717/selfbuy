<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminNotification extends Model
{
    protected $table = 'admin_notifications';

    protected $fillable = ['type', 'title', 'body', 'url', 'icon', 'data', 'read_at'];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('id');
    }

    /* ── Helpers ────────────────────────────────────────────── */

    public function getIsUnreadAttribute(): bool
    {
        return is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Create a notification. Silently no-ops on failure so a notification
     * problem can never break the action that triggered it.
     */
    public static function record(array $attributes): ?self
    {
        try {
            return static::create(array_merge(['type' => 'general', 'icon' => 'fa-bell'], $attributes));
        } catch (Throwable $e) {
            Log::warning('AdminNotification push failed: ' . $e->getMessage());
            return null;
        }
    }
}
