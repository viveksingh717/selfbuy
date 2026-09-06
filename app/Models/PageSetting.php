<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageSetting extends Model
{
    use SoftDeletes;

    protected $table = 'page_settings';

    protected $fillable = [
        'slug',
        'name',
        'label',
        'title',
        'short_description',
        'description',
        'content',
        'file_path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /** Only active (and not soft-deleted) pages. */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /** Fetch a single page by its slug - handy from the front-end. */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /** Public URL of the uploaded file, or null when there isn't one. */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
