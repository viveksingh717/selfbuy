<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'team_members';

    protected $fillable = [
        'name',
        'designation',
        'bio',
        'photo',
        'email',
        'phone',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status'     => 'integer',
        'sort_order' => 'integer',
    ];

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /* ── Accessors ──────────────────────────────────────────── */

    /** Uploaded photo, or one of the theme's stock team images as a fallback. */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/team/' . $this->photo);
        }

        $n = (($this->id ?? 1) - 1) % 3 + 1;

        return asset('assets/images/team/member-' . $n . '.jpg');
    }

    public function getInitialsAttribute(): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim((string) $this->name)) ?: []));

        if (!$parts) {
            return '?';
        }

        $first = Str::substr($parts[0], 0, 1);
        $last  = count($parts) > 1 ? Str::substr(end($parts), 0, 1) : '';

        return Str::upper($first . $last);
    }

    /** [label => url] of the socials that are actually set. */
    public function getSocialLinksAttribute(): array
    {
        return array_filter([
            'facebook'  => $this->facebook_url,
            'twitter'   => $this->twitter_url,
            'instagram' => $this->instagram_url,
            'linkedin'  => $this->linkedin_url,
        ]);
    }
}
