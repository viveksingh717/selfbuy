<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePartner extends Model
{
    protected $table = 'home_partners';

    protected $fillable = [
        'name',
        'image',
        'link',
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

    /** "assets/..." is a bundled theme image, anything else is an upload. */
    public function getImageUrlAttribute(): ?string
    {
        return HomeSlide::imageUrl($this->image);
    }
}
