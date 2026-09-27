<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'session_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'order_notes',
        'subtotal',
        'discount',
        'coupon_code',
        'shipping_cost',
        'shipping_method',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'tracking_courier',
        'tracking_number',
        'tracking_url',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'cancel_reason',
        'stock_restored',
    ];

    // Order lifecycle - also the only allowed transitions (admin panel enforces these).
    public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    public const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];
    public const NEXT_STATUSES = [
        'pending'    => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped'    => ['delivered'],
        'delivered'  => [],
        'cancelled'  => [],
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'stock_restored' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->latest('id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id')->latest('id');
    }

    /**
     * Link to the customer's order page for use in emails. Signed, so it opens for whoever
     * received the email (not logged in / another device) - and only for this order.
     */
    public function viewUrl(): string
    {
        return \Illuminate\Support\Facades\URL::signedRoute('checkout.success', ['orderNumber' => $this->order_number]);
    }

    public function customerName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Tracking journey for the progress tracker (admin + customer order pages):
     * Placed -> Processing -> Shipped -> Delivered, or ending in Cancelled.
     * Each step: key, label, date (Carbon|null), state = done | current | upcoming | cancelled.
     */
    public function milestones(): array
    {
        $reachedAt = fn (string $status) => $this->relationLoaded('histories')
            ? optional($this->histories->where('type', 'order')->where('to_value', $status)->sortBy('id')->first())->created_at
            : optional($this->histories()->where('type', 'order')->where('to_value', $status)->oldest('id')->first())->created_at;

        $steps = [
            'pending'    => ['Order placed', $this->created_at],
            'processing' => ['Processing',   $reachedAt('processing')],
            'shipped'    => ['Shipped',      $this->shipped_at ?? $reachedAt('shipped')],
            'delivered'  => ['Delivered',    $this->delivered_at ?? $reachedAt('delivered')],
        ];
        $order = array_keys($steps);

        if ($this->order_status === 'cancelled') {
            // Show how far it got, then the cancellation.
            $milestones = [];
            foreach ($steps as $key => [$label, $date]) {
                if ($key === 'pending' || $date) {
                    $milestones[] = ['key' => $key, 'label' => $label, 'date' => $date, 'state' => 'done'];
                }
            }
            $milestones[] = ['key' => 'cancelled', 'label' => 'Cancelled', 'date' => $this->cancelled_at, 'state' => 'cancelled'];

            return $milestones;
        }

        // "Order placed" has always happened; a pending order is waiting at the Processing step.
        $reached = array_search($this->order_status, $order, true);
        $milestones = [];
        foreach ($order as $i => $key) {
            [$label, $date] = $steps[$key];
            if ($i === 0 || $i < $reached || $key === 'delivered' && $i === $reached) {
                $milestones[] = ['key' => $key, 'label' => $label, 'date' => $date, 'state' => 'done'];
            } elseif ($i === max($reached, 1)) {
                $milestones[] = ['key' => $key, 'label' => $label, 'date' => $i === $reached ? $date : null, 'state' => 'current',
                    'caption' => $reached === 0 ? 'Awaiting confirmation' : 'In progress'];
            } else {
                $milestones[] = ['key' => $key, 'label' => $label, 'date' => null, 'state' => 'upcoming'];
            }
        }

        return $milestones;
    }

    /** Statuses an admin can move this order to next. */
    public function nextStatuses(): array
    {
        return self::NEXT_STATUSES[$this->order_status] ?? [];
    }

    /**
     * Fixed colour per status (the admin theme remaps Bootstrap's success/danger classes,
     * so badges use these explicit colours instead).
     */
    public static function statusColor(string $status): string
    {
        return match ($status) {
            'pending'    => '#f2a007',
            'processing' => '#2185d0',
            'shipped'    => '#6435c9',
            'delivered', 'paid' => '#21ba45',
            'cancelled', 'failed' => '#db2828',
            'refunded'   => '#6c757d',
            default      => '#9aa0ac',
        };
    }

    /** Coloured status badge HTML (admin panel). */
    public static function statusBadge(string $status): string
    {
        return '<span class="tag" style="background:' . self::statusColor($status) . ';color:#fff">' . e(ucfirst($status)) . '</span>';
    }

    public function paymentMethodLabel(): string
    {
        // Deliberately doesn't name the specific gateway (Razorpay, Stripe, PayPal,
        // UPI, ...) to the customer — they don't need to know which processor was
        // used, just how they paid. Keeps this label stable as more gateways are added.
        return match ($this->payment_method) {
            'cod' => 'Cash on Delivery',
            default => 'Online Payment',
        };
    }
}
