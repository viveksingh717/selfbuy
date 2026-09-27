<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Admin > Transaction History: one list of every money movement -
 *  - online payment attempts (payments table: Razorpay / Stripe / PayPal / Instamojo), and
 *  - Cash on Delivery orders (they have no payments row; the money is tracked on the order).
 *
 * Status is normalised to paid | pending | failed | refunded (a refund is recorded on the
 * order from the admin order page, so a paid payment whose order was refunded shows "refunded").
 * Read-only - nothing here changes payments or orders.
 */
class TransactionHistoryService
{
    public const GATEWAYS = ['razorpay', 'stripe', 'paypal', 'instamojo', 'cod'];
    public const STATUSES = ['paid', 'pending', 'failed', 'refunded'];

    /** The unified list (as a sub-query), with the admin's filters applied. */
    public function query(array $filters = []): Builder
    {
        $online = DB::table('payments as p')
            ->leftJoin('orders as o', 'o.id', '=', 'p.order_id')
            ->selectRaw("
                CONCAT('pay-', p.id)                      AS uid,
                'online'                                  AS source,
                p.id                                      AS payment_id,
                p.order_id                                AS order_id,
                o.order_number                            AS order_number,
                p.gateway                                 AS gateway,
                COALESCE(p.gateway_payment_id, p.gateway_order_id) AS reference,
                COALESCE(CONCAT(o.first_name, ' ', o.last_name),
                         CONCAT(JSON_UNQUOTE(JSON_EXTRACT(p.billing_data, '$.first_name')), ' ',
                                JSON_UNQUOTE(JSON_EXTRACT(p.billing_data, '$.last_name'))))  AS customer_name,
                COALESCE(o.email, JSON_UNQUOTE(JSON_EXTRACT(p.billing_data, '$.email')))   AS customer_email,
                p.amount                                  AS amount,
                p.currency                                AS currency,
                CASE
                    WHEN o.payment_status = 'refunded' AND p.status = 'paid' THEN 'refunded'
                    WHEN p.status = 'created' THEN 'pending'
                    ELSE p.status
                END                                       AS status,
                p.failure_reason                          AS note,
                p.paid_at                                 AS paid_at,
                p.created_at                              AS created_at
            ");

        $cod = DB::table('orders as o')
            ->where('o.payment_method', 'cod')
            ->selectRaw("
                CONCAT('cod-', o.id)                      AS uid,
                'cod'                                     AS source,
                NULL                                      AS payment_id,
                o.id                                      AS order_id,
                o.order_number                            AS order_number,
                'cod'                                     AS gateway,
                NULL                                      AS reference,
                CONCAT(o.first_name, ' ', o.last_name)    AS customer_name,
                o.email                                   AS customer_email,
                o.total                                   AS amount,
                'INR'                                     AS currency,
                o.payment_status                          AS status,
                CASE WHEN o.order_status = 'cancelled' THEN 'Order cancelled' END AS note,
                CASE WHEN o.payment_status = 'paid' THEN COALESCE(o.delivered_at, o.updated_at) END AS paid_at,
                o.created_at                              AS created_at
            ");

        return DB::query()
            ->fromSub($online->unionAll($cod), 't')
            ->when(!empty($filters['q']), function (Builder $q) use ($filters) {
                $term = '%' . trim($filters['q']) . '%';
                $q->where(fn (Builder $w) => $w->where('reference', 'like', $term)
                    ->orWhere('order_number', 'like', $term)
                    ->orWhere('customer_email', 'like', $term)
                    ->orWhere('customer_name', 'like', $term));
            })
            ->when(!empty($filters['gateway']), fn (Builder $q) => $q->where('gateway', $filters['gateway']))
            ->when(!empty($filters['status']), fn (Builder $q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['date_from']), fn (Builder $q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn (Builder $q) => $q->whereDate('created_at', '<=', $filters['date_to']));
    }

    /**
     * Totals for the summary cards: per status, per currency (never adds INR and USD together),
     * plus collected-online vs collected-COD.
     *
     * @return array{byStatus: array, collected: array}
     */
    public function summary(array $filters = []): array
    {
        $rows = DB::query()->fromSub($this->query($filters), 's')
            ->selectRaw('status, source, currency, COUNT(*) AS n, SUM(amount) AS total')
            ->groupBy('status', 'source', 'currency')
            ->get();

        $byStatus = [];
        foreach (self::STATUSES as $s) {
            $byStatus[$s] = ['count' => 0, 'amounts' => []];
        }
        $collected = ['online' => [], 'cod' => []];

        foreach ($rows as $r) {
            $byStatus[$r->status]['count'] += (int) $r->n;
            $byStatus[$r->status]['amounts'][$r->currency] = ($byStatus[$r->status]['amounts'][$r->currency] ?? 0) + (float) $r->total;
            if ($r->status === 'paid') {
                $collected[$r->source][$r->currency] = ($collected[$r->source][$r->currency] ?? 0) + (float) $r->total;
            }
        }

        return ['byStatus' => $byStatus, 'collected' => $collected];
    }

    public static function money(float $amount, string $currency): string
    {
        $symbol = ['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'][$currency] ?? ($currency . ' ');

        return $symbol . number_format($amount, 2);
    }

    /** "₹1,20,000.00 + $80.59" style list for a [currency => amount] map. */
    public static function moneyList(array $amounts): string
    {
        if (empty($amounts)) {
            return self::money(0, 'INR');
        }
        ksort($amounts);

        return collect($amounts)->map(fn ($v, $c) => self::money($v, $c))->implode(' + ');
    }

    public static function gatewayLabel(string $gateway): string
    {
        return ['cod' => 'Cash on Delivery', 'razorpay' => 'Razorpay', 'stripe' => 'Stripe', 'paypal' => 'PayPal', 'instamojo' => 'Instamojo'][$gateway] ?? ucfirst($gateway);
    }

    public static function statusColor(string $status): string
    {
        return ['paid' => '#21ba45', 'pending' => '#f2a007', 'failed' => '#db2828', 'refunded' => '#6c757d'][$status] ?? '#9aa0ac';
    }
}
