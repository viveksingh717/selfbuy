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

    /**
     * Almost every gateway here charges in INR, matching the store's own
     * pricing — PayPal is the one exception (its India-registered merchant
     * accounts can't receive INR at all, so it charges in USD instead; see
     * PaymentService::initiate()). Anywhere a Payment's own amount is shown
     * to a customer (as opposed to an Order's, which is always INR) needs
     * this instead of a hardcoded ₹.
     */
    public function currencySymbol(): string
    {
        return match ($this->currency) {
            'USD' => '$',
            'INR' => '₹',
            default => $this->currency.' ',
        };
    }
}
