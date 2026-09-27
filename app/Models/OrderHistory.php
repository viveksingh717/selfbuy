<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One entry in an order's activity timeline (admin panel > order detail). */
class OrderHistory extends Model
{
    protected $table = 'order_histories';

    protected $fillable = [
        'order_id',
        'type',              // order | payment | tracking | email | note
        'from_value',
        'to_value',
        'note',
        'admin_id',
        'customer_notified',
    ];

    protected $casts = [
        'customer_notified' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
