<?php

namespace App\Http\Controllers\Admin;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\ExportService;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $dashboard) {
        $adminDetails = Auth::guard('admin')->user();

        [$range, $from, $to, $prevFrom, $prevTo] = $this->resolveRange($request, $dashboard);

        return view('admin.dashboard', [
            'adminDetails' => $adminDetails,
            'range'        => $range,
            'periodLabel'  => $this->periodLabel($range, $from, $to),
            'from'         => $from,
            'to'           => $to,
            'overview'     => $dashboard->overview(),
            'kpis'         => $dashboard->kpis($from, $to, $prevFrom, $prevTo),
            'sales'        => $dashboard->salesSeries($range, $from, $to),
            'statuses'     => $dashboard->statusBreakdown(),
            'attention'    => $dashboard->attention(),
            'recentOrders' => $dashboard->recentOrders(),
            'topProducts'  => $dashboard->topProducts($from, $to, 5),
            'topAllTime'   => $dashboard->topProducts(null, null, 5),
            'paymentMix'   => $dashboard->paymentMix($from, $to),
            'lowStock'     => $dashboard->lowStock(),
        ]);
    }

    /** Report for the selected range - CSV (sections) or PDF. */
    public function export(Request $request, DashboardService $dashboard, ExportService $export, string $format)
    {
        abort_unless(in_array($format, ['csv', 'pdf'], true), 404);

        [$range, $from, $to, $prevFrom, $prevTo] = $this->resolveRange($request, $dashboard);
        $label = $this->periodLabel($range, $from, $to);
        $data = [
            'periodLabel' => $label,
            'from'        => $from,
            'to'          => $to,
            'kpis'        => $dashboard->kpis($from, $to, $prevFrom, $prevTo),
            'statuses'    => $dashboard->periodStatusBreakdown($from, $to),
            'topProducts' => $dashboard->topProducts($from, $to, 10),
            'paymentMix'  => $dashboard->paymentMix($from, $to),
            'sales'       => $dashboard->salesSeries($range, $from, $to),
            'orders'      => Order::withCount('items')->whereBetween('created_at', [$from, $to])->latest()->limit(ExportService::PDF_ROW_LIMIT)->get(),
            'orderCount'  => Order::whereBetween('created_at', [$from, $to])->count(),
        ];
        $file = 'dashboard-report-' . $from->format('Ymd') . '-' . $to->format('Ymd');

        if ($format === 'pdf') {
            return $export->pdf('admin.exports.dashboard', $data, "{$file}.pdf", 'portrait');
        }

        return $export->csv("{$file}.csv", function ($put) use ($data, $from, $to) {
            $k = $data['kpis'];
            $put(['SelfBuy dashboard report', $data['periodLabel'], $from->format('Y-m-d') . ' to ' . $to->format('Y-m-d')]);
            $put([]);
            $put(['Summary', 'Value', 'Previous period', 'Change %']);
            foreach (['revenue' => 'Revenue (paid)', 'orders' => 'Orders', 'aov' => 'Avg. order value', 'customers' => 'Customers'] as $key => $name) {
                $put([$name, round($k[$key]['value'], 2), round($k[$key]['prev'], 2), $k[$key]['change'] ?? 'n/a']);
            }
            $put(['Cancelled orders', $k['cancelled']]);
            $put(['New customer accounts', $k['new_accounts']]);
            $put([]);
            $put(['Order status', 'Orders']);
            foreach ($data['statuses'] as $status => $n) {
                $put([ucfirst($status), $n]);
            }
            $put([]);
            $put(['Top products', 'Units', 'Orders', 'Revenue']);
            foreach ($data['topProducts'] as $p) {
                $put([$p->product_name, $p->units, $p->orders, round((float) $p->revenue, 2)]);
            }
            $put([]);
            $put(['Payment method', 'Orders', 'Value']);
            foreach ($data['paymentMix'] as $m) {
                $put([strtoupper($m->payment_method), $m->orders, round((float) $m->value, 2)]);
            }
            $put([]);
            $put(['Sales by ' . (count($data['sales']['labels']) && str_contains($data['sales']['labels'][0], ':') ? 'hour' : 'period'), 'Orders', 'Revenue (paid)']);
            foreach ($data['sales']['labels'] as $i => $lbl) {
                $put([$lbl, $data['sales']['orders'][$i], $data['sales']['revenue'][$i]]);
            }
            $put([]);
            $put(['Orders in period', 'Date', 'Customer', 'Email', 'Items', 'Total', 'Payment', 'Payment status', 'Order status']);
            Order::withCount('items')->whereBetween('created_at', [$from, $to])->latest()->orderByDesc('id')
                ->chunk(500, function ($orders) use ($put) {
                    foreach ($orders as $o) {
                        $put([$o->order_number, $o->created_at->format('Y-m-d H:i'), $o->customerName(), $o->email, $o->items_count,
                              $o->total, strtoupper($o->payment_method), $o->payment_status, $o->order_status]);
                    }
                });
        });
    }

    /**
     * [range, from, to, prevFrom, prevTo] from ?range= (a preset) or ?range=custom&from=&to=.
     * Invalid custom dates fall back to the default 30 days.
     */
    private function resolveRange(Request $request, DashboardService $dashboard): array
    {
        $range = $request->query('range');

        if ($range === 'custom') {
            $v = Validator::make($request->query(), [
                'from' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
                'to'   => ['required', 'date_format:Y-m-d', 'after_or_equal:from', 'before_or_equal:today'],
            ]);
            if ($v->passes() && Carbon::parse($request->query('from'))->diffInDays(Carbon::parse($request->query('to'))) <= 731) {
                return ['custom', ...$dashboard->period('custom', $request->query('from'), $request->query('to'))];
            }
            $range = '30d';
        }

        $range = array_key_exists($range, DashboardService::RANGES) ? $range : '30d';

        return [$range, ...$dashboard->period($range)];
    }

    private function periodLabel(string $range, Carbon $from, Carbon $to): string
    {
        return $range === 'custom'
            ? ($from->isSameDay($to) ? $from->format('d M Y') : $from->format('d M Y') . ' – ' . $to->format('d M Y'))
            : DashboardService::RANGES[$range];
    }

    public function chat() {
        $adminDetails = Auth::guard('admin')->user();
        return view('admin.chats.chat', compact('adminDetails'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Log the user out of the 'admin' guard

        $request->session()->invalidate(); // Invalidate the session

        $request->session()->regenerateToken(); // Regenerate the CSRF token to prevent CSRF attacks

        return redirect()->route('admin.login')->with('status', 'Successfully logged out!');

    }
}
