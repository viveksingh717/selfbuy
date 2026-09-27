<?php

namespace App\Services;

use App\Models\ContactUs;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Numbers for the admin dashboard.
 *
 * Definitions (kept in one place so every card agrees):
 *  - Revenue   = total of orders whose payment_status is "paid" (refunded excluded)
 *  - Orders    = orders placed in the period (all statuses)
 *  - Customers = distinct buyer emails in the period (guest checkouts included)
 *  - Low stock = variant stock / product qty at or below the product's own low_stock_alert
 */
class DashboardService
{
    public const RANGES = [
        'today' => 'Today',
        '7d'    => '7 days',
        '30d'   => '30 days',
        '90d'   => '90 days',
        'year'  => 'This year',
    ];

    /** [from, to, previousFrom, previousTo] for a range key. */
    /**
     * [from, to, previousFrom, previousTo] for a range key. 'custom' uses the given dates
     * (Y-m-d); the previous period is the same length immediately before it.
     */
    public function period(string $range, ?string $customFrom = null, ?string $customTo = null): array
    {
        $now = now();
        [$from, $to] = $range === 'custom' && $customFrom && $customTo
            ? [Carbon::parse($customFrom)->startOfDay(), Carbon::parse($customTo)->endOfDay()]
            : match ($range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7d'    => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '90d'   => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
            'year'  => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
        };
        $length = $from->diffInSeconds($to, true);
        $prevTo = $from->copy()->subSecond();
        $prevFrom = $prevTo->copy()->subSeconds((int) $length);

        return [$from, $to, $prevFrom, $prevTo];
    }

    /** Headline KPIs for the period, each with the previous period's value for a % change. */
    public function kpis(Carbon $from, Carbon $to, Carbon $prevFrom, Carbon $prevTo): array
    {
        $calc = function (Carbon $a, Carbon $b) {
            $orders = Order::whereBetween('created_at', [$a, $b]);
            $paid = (clone $orders)->where('payment_status', 'paid');
            $orderCount = (clone $orders)->count();
            $revenue = (float) (clone $paid)->sum('total');
            $paidCount = (clone $paid)->count();

            return [
                'revenue'   => $revenue,
                'orders'    => $orderCount,
                'aov'       => $paidCount ? $revenue / $paidCount : 0.0,
                'customers' => (clone $orders)->distinct('email')->count('email'),
            ];
        };

        $now = $calc($from, $to);
        $prev = $calc($prevFrom, $prevTo);

        $out = [];
        foreach ($now as $key => $value) {
            $out[$key] = [
                'value'  => $value,
                'prev'   => $prev[$key],
                'change' => $prev[$key] > 0 ? round(($value - $prev[$key]) / $prev[$key] * 100, 1) : null, // null = no baseline
            ];
        }
        $out['new_accounts'] = User::where('role_type', 0)->whereBetween('created_at', [$from, $to])->count();
        $out['cancelled'] = Order::whereBetween('created_at', [$from, $to])->where('order_status', 'cancelled')->count();

        return $out;
    }

    /**
     * Revenue + order counts per bucket for the chart: hourly for today, monthly for the
     * year view, daily otherwise. Empty buckets are filled with 0 so the line is continuous.
     */
    public function salesSeries(string $range, Carbon $from, Carbon $to): array
    {
        if ($range === 'custom') {
            // a single day -> hourly; up to ~3 months -> daily; longer -> monthly
            $days = $from->diffInDays($to, true);
            $range = $days < 1 ? 'today' : ($days > 92 ? 'months' : 'days');
        }

        [$sqlFormat, $step, $labelFormat] = match ($range) {
            'months' => ['%Y-%m', '1 month', 'M Y'],
            'today' => ['%Y-%m-%d %H:00', '1 hour', 'H:00'],
            'year'  => ['%Y-%m', '1 month', 'M'],   // one calendar year, so the month name is enough
            default => ['%Y-%m-%d', '1 day', 'd M'],
        };
        $keyFormat = match ($range) { 'today' => 'Y-m-d H:00', 'year', 'months' => 'Y-m', default => 'Y-m-d' };

        $rows = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '{$sqlFormat}') AS bucket, COUNT(*) AS orders,
                         SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) AS revenue")
            ->groupBy('bucket')
            ->get()
            ->keyBy('bucket');

        $labels = $orders = $revenue = [];
        $start = in_array($range, ['year', 'months'], true) ? $from->copy()->startOfMonth() : ($range === 'today' ? $from->copy()->startOfHour() : $from->copy()->startOfDay());
        foreach (CarbonPeriod::create($start, $step, $to) as $point) {
            $key = $point->format($keyFormat);
            $labels[] = $point->format($labelFormat);
            $orders[] = (int) ($rows[$key]->orders ?? 0);
            $revenue[] = round((float) ($rows[$key]->revenue ?? 0), 2);
        }

        return compact('labels', 'orders', 'revenue');
    }

    /**
     * All-time store totals for the top row: orders, pending, revenue, customers,
     * customers active right now (seen in the last 5 minutes) and open complaints.
     */
    public function overview(): array
    {
        $registered = User::where('role_type', '!=', 1);                       // everyone except admins
        $guestBuyers = Order::whereNull('user_id')
            ->whereNotIn('email', User::select('email'))
            ->distinct('email')->count('email');

        return [
            'orders'          => Order::count(),
            'pending'         => Order::where('order_status', 'pending')->count(),
            'revenue'         => (float) Order::where('payment_status', 'paid')->sum('total'),
            'customers'       => (clone $registered)->count(),
            'guest_buyers'    => $guestBuyers,
            'new_today'       => (clone $registered)->whereDate('created_at', today())->count(),
            'active_now'      => (clone $registered)->where('last_seen_at', '>=', now()->subMinutes(5))->count(),
            'active_today'    => (clone $registered)->where('last_seen_at', '>=', today())->count(),
            'complaints'      => ContactUs::count(),
            'complaints_open' => ContactUs::where('status', '!=', 'done')->count(),
        ];
    }

    /** Current count per order status (all time) - the pipeline. */
    public function statusBreakdown(): array
    {
        $counts = Order::selectRaw('order_status, COUNT(*) AS n')->groupBy('order_status')->pluck('n', 'order_status');

        return collect(Order::STATUSES)->mapWithKeys(fn ($s) => [$s => (int) ($counts[$s] ?? 0)])->all();
    }

    /** Orders placed in the period, by their current status (for the exported report). */
    public function periodStatusBreakdown(Carbon $from, Carbon $to): array
    {
        $counts = Order::whereBetween('created_at', [$from, $to])->selectRaw('order_status, COUNT(*) AS n')->groupBy('order_status')->pluck('n', 'order_status');

        return collect(Order::STATUSES)->mapWithKeys(fn ($s) => [$s => (int) ($counts[$s] ?? 0)])->all();
    }

    /** Things an admin should act on, with counts and where to go. */
    public function attention(): array
    {
        return [
            ['Orders to confirm', Order::where('order_status', 'pending')->count(), 'fa-clock-o', '#f2a007', route('admin.orders', ['order_status' => 'pending'])],
            ['Orders to ship', Order::where('order_status', 'processing')->count(), 'fa-cube', '#2185d0', route('admin.orders', ['order_status' => 'processing'])],
            ['COD awaiting payment', Order::where('payment_method', 'cod')->where('payment_status', 'pending')->where('order_status', '!=', 'cancelled')->count(), 'fa-money', '#6435c9', route('admin.transactions', ['gateway' => 'cod', 'status' => 'pending'])],
            ['Failed payments (7 days)', Payment::where('status', 'failed')->where('created_at', '>=', now()->subDays(7))->count(), 'fa-exclamation-triangle', '#db2828', route('admin.transactions', ['status' => 'failed'])],
            ['Low / out of stock items', $this->lowStockQuery()->count(), 'fa-archive', '#e67e22', '#low-stock'],
            ['Unread messages', ContactUs::where('status', 'unread')->count(), 'fa-envelope', '#00b5ad', route('admin.contact_us')],
        ];
    }

    public function recentOrders(int $limit = 8): Collection
    {
        return Order::withCount('items')->latest()->limit($limit)->get();
    }

    /** Best sellers - in the period, or all time when no dates are given (cancelled orders excluded). */
    public function topProducts(?Carbon $from = null, ?Carbon $to = null, int $limit = 5): Collection
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('product_models', 'product_models.id', '=', 'order_items.product_id')
            ->when($from && $to, fn ($q) => $q->whereBetween('orders.created_at', [$from, $to]))
            ->where('orders.order_status', '!=', 'cancelled')
            ->groupBy('order_items.product_id', 'order_items.product_name', 'product_models.product_image', 'product_models.id')
            ->selectRaw('order_items.product_id, order_items.product_name, product_models.product_image, product_models.id AS exists_id,
                         SUM(order_items.qty) AS units, SUM(order_items.line_total) AS revenue, COUNT(DISTINCT orders.id) AS orders')
            ->orderByDesc('units')
            ->limit($limit)
            ->get();
    }

    /** COD vs online (by gateway) for orders in the period. */
    public function paymentMix(Carbon $from, Carbon $to): Collection
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->where('order_status', '!=', 'cancelled')
            ->selectRaw('payment_method, COUNT(*) AS orders, SUM(total) AS value')
            ->groupBy('payment_method')
            ->orderByDesc('orders')
            ->get();
    }

    /** Variants / simple products at or below their product's low-stock alert. */
    public function lowStock(int $limit = 8): Collection
    {
        return $this->lowStockQuery()->orderBy('stock')->limit($limit)->get();
    }

    private function lowStockQuery()
    {
        // Variants (stock lives on the attribute)
        $variants = DB::table('product_attributes as a')
            ->join('product_models as p', 'p.id', '=', 'a.product_id')
            ->leftJoin('color_models as c', 'c.id', '=', 'a.color_id')
            ->leftJoin('size_models as s', 's.id', '=', 'a.size_id')
            ->where('p.status', 1)
            ->whereColumn('a.stock', '<=', DB::raw('GREATEST(p.low_stock_alert, 0)'))
            ->selectRaw("p.id AS product_id, p.product_name, TRIM(CONCAT(COALESCE(c.color_name, ''), ' ', COALESCE(s.size_name, ''))) AS variant,
                         a.stock AS stock, p.low_stock_alert AS alert_at");

        // Products without variants (stock lives on the product)
        $simple = DB::table('product_models as p')
            ->where('p.status', 1)
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('product_attributes as a2')->whereColumn('a2.product_id', 'p.id'))
            ->whereColumn('p.qty', '<=', DB::raw('GREATEST(p.low_stock_alert, 0)'))
            ->selectRaw("p.id AS product_id, p.product_name, NULL AS variant, p.qty AS stock, p.low_stock_alert AS alert_at");

        return DB::query()->fromSub($variants->unionAll($simple), 'ls');
    }
}
