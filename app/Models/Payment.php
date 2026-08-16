<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'session_id',
        'gateway',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'amount',
        'currency',
        'status',
        'billing_data',
        'order_snapshot',
        'meta',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'billing_data' => 'array',
        'order_snapshot' => 'array',
        'meta' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
