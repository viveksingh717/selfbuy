<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewVote extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'session_id',
        'is_helpful',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
