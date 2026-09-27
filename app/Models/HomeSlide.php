<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSlide extends Model
{
    protected $table = 'home_slides';

    protected $fillable = [
        'image',
        'image_mobile',
        'subtitle',
        'title',
        'description',
        'button_text',
        'button_link',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Stored path -> URL. "assets/..." is a bundled theme image, anything else is an upload. */
    public static function imageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return str_starts_with($path, 'assets/') ? asset($path) : asset('storage/' . $path);
    }

    public function getImageUrlAttribute(): ?string
    {
        return self::imageUrl($this->image);
    }

    /** Falls back to the desktop image when no mobile image is set. */
    public function getImageMobileUrlAttribute(): ?string
    {
        return self::imageUrl($this->image_mobile) ?? $this->image_url;
    }
}
