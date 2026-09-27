<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'user_id',
        'coupon_id',
        'status',
        'source',
        'ip_address',
        'user_agent',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at'   => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /** The single-use sign-up coupon issued to this subscriber (if any). */
    public function coupon()
    {
        return $this->belongsTo(CouponModel::class, 'coupon_id');
    }

    public function scopeSubscribed($query)
    {
        return $query->where('status', 'subscribed');
    }

    public function isSubscribed(): bool
    {
        return $this->status === 'subscribed';
    }
}
