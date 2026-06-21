<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponModel extends Model
{
    use HasFactory;

    protected $table = 'coupon_models';
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'expiry_date',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'applicable_to',
        'status',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'expiry_date'         => 'date',
        'discount_value'      => 'decimal:2',
        'min_order_amount'    => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    // ── Accessors ──
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 1;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getRemainingUsageAttribute(): ?int
    {
        if (is_null($this->usage_limit)) return null;
        return max(0, $this->usage_limit - $this->used_count);
    }
}
