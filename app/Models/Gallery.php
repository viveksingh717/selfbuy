<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'galleries';

    protected $fillable = [
        'title',
        'album',
        'caption',
        'image',
        'link_url',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status'     => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeAlbum($query, ?string $album)
    {
        return $album ? $query->where('album', $album) : $query;
    }

    /** Uploaded image (stored under gallery/) or, for seeded rows, an asset path. */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('assets/images/portfolio/item-1.jpg');
        }

        return Str::contains($this->image, '/')
            ? asset($this->image)
            : asset('storage/gallery/' . $this->image);
    }
}
