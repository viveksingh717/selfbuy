<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AdminOrderService;
use App\Services\ExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function __construct(private AdminOrderService $orders)
    {
    }

    /** Order list page, or its DataTables JSON (with the filter bar's values) for AJAX calls. */
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            // alias must not be "total": the Order model casts that column to decimal(2)
            $counts = Order::selectRaw('order_status, COUNT(*) as orders_count')->groupBy('order_status')->pluck('orders_count', 'order_status')->map(fn ($n) => (int) $n);

            return view('admin.orders.orders', [
                'counts'   => $counts,
                'total'    => $counts->sum(),
                'methods'  => Order::select('payment_method')->distinct()->orderBy('payment_method')->pluck('payment_method'),
            ]);
        }

        $query = $this->filteredOrders($request)->withCount('items');

        return DataTables::of($query)
            ->editColumn('order_number', fn ($o) => '<a href="' . route('admin.orders.show', $o->id) . '" class="font-weight-bold">#' . e($o->order_number) . '</a>')
            ->addColumn('customer', fn ($o) => '<div>' . e($o->customerName()) . '</div><div class="text-muted small">' . e($o->email) . '</div>')
            ->editColumn('total', fn ($o) => '₹' . number_format((float) $o->total, 2))
            ->addColumn('payment', fn ($o) => '<div class="small">' . e(strtoupper($o->payment_method)) . '</div>' . $this->badge($o->payment_status))
            ->editColumn('order_status', fn ($o) => $this->badge($o->order_status))
            ->editColumn('created_at', fn ($o) => '<span title="' . $o->created_at->format('d M Y, h:i A') . '">' . $o->created_at->format('d M Y') . '</span>'
                . '<div class="text-muted small">' . $o->created_at->diffForHumans() . '</div>')
            ->addColumn('action', fn ($o) => '<a href="' . route('admin.orders.show', $o->id) . '" class="btn btn-sm btn-primary" title="View order"><i class="fa fa-eye"></i></a>')
            ->filterColumn('customer', fn ($q, $kw) => $q->whereRaw("CONCAT(first_name, ' ', last_name, ' ', email) LIKE ?", ["%{$kw}%"]))
            ->orderColumn('customer', 'first_name $1')
            ->orderColumn('payment', 'payment_status $1')
            ->rawColumns(['order_number', 'customer', 'payment', 'order_status', 'created_at', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'items.productAttribute', 'histories.admin', 'payments', 'user'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $data = $request->validate([
            'order_status' => ['required', Rule::in(Order::STATUSES)],
            'note'         => ['nullable', 'string', 'max:500'],
            'notify'       => ['nullable', 'boolean'],
        ]);

        return $this->respond($request, $this->orders->changeStatus(
            $order, $data['order_status'], $data['note'] ?? null, $request->boolean('notify'), Auth::guard('admin')->id()
        ));
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'payment_status' => ['required', Rule::in(Order::PAYMENT_STATUSES)],
            'note'           => ['nullable', 'string', 'max:500'],
        ]);

        return $this->respond($request, $this->orders->changePaymentStatus(
            $order, $data['payment_status'], $data['note'] ?? null, Auth::guard('admin')->id()
        ));
    }

    public function updateTracking(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'tracking_courier' => ['nullable', 'string', 'max:100'],
            'tracking_number'  => ['nullable', 'string', 'max:100'],
            'tracking_url'     => ['nullable', 'url:http,https', 'max:500'],
            'notify'           => ['nullable', 'boolean'],
        ]);

        return $this->respond($request, $this->orders->updateTracking(
            $order, $data + ['tracking_courier' => null, 'tracking_number' => null, 'tracking_url' => null],
            $request->boolean('notify'), Auth::guard('admin')->id()
        ));
    }

    public function sendEmail(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate(['type' => ['required', Rule::in(['confirmation', 'status'])]]);

        return $this->respond($request, $this->orders->sendEmail($order, $data['type'], Auth::guard('admin')->id()));
    }

    public function addNote(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate(['note' => ['required', 'string', 'max:1000']]);

        return $this->respond($request, $this->orders->addNote($order, $data['note'], Auth::guard('admin')->id()));
    }

    public function invoice($id)
    {
        $order = Order::with('items')->findOrFail($id);

        return Pdf::loadView('admin.orders.invoice', compact('order'))
            ->setOption('isFontSubsettingEnabled', true) // embed only the glyphs used -> small PDF
            ->setPaper('a4')
            ->download("invoice-{$order->order_number}.pdf");
    }

    public function destroy(Request $request, $id)
    {
        $result = $this->orders->delete(Order::findOrFail($id));

        if (!$request->expectsJson() && $result['success']) {
            return redirect()->route('admin.orders')->with('success', $result['message']);
        }

        return $this->respond($request, $result);
    }

    /** CSV or PDF of the order list, using the same filters as the table on screen. */
    public function export(Request $request, ExportService $export, string $format)
    {
        abort_unless(in_array($format, ['csv', 'pdf'], true), 404);

        $filters = $this->orderFilters($request);
        $query = $this->filteredOrders($request)->withCount('items')->latest('created_at')->orderByDesc('id');
        $stamp = now()->format('Y-m-d-His');

        if ($format === 'csv') {
            return $export->csv("orders-{$stamp}.csv", function ($put) use ($query) {
                $put(['Order', 'Date', 'Customer', 'Email', 'Phone', 'City', 'State', 'Items', 'Subtotal', 'Discount', 'Coupon',
                      'Shipping', 'Total', 'Payment method', 'Payment status', 'Order status', 'Courier', 'Tracking no.']);
                $query->chunk(500, function ($orders) use ($put) {
                    foreach ($orders as $o) {
                        $put([$o->order_number, $o->created_at->format('Y-m-d H:i'), $o->customerName(), $o->email, $o->phone,
                              $o->city, $o->state, $o->items_count, $o->subtotal, $o->discount, $o->coupon_code, $o->shipping_cost,
                              $o->total, strtoupper($o->payment_method), $o->payment_status, $o->order_status,
                              $o->tracking_courier, $o->tracking_number]);
                    }
                });
            });
        }

        $totalCount = (clone $query)->count();
        $summaryBase = $this->filteredOrders($request);

        return $export->pdf('admin.exports.orders', [
            'orders'     => $query->limit(ExportService::PDF_ROW_LIMIT)->get(),
            'totalCount' => $totalCount,
            'filterLine' => ExportService::describeFilters($filters, ['q' => 'Search', 'date_from' => 'From', 'date_to' => 'To']),
            'summary'    => [
                'value'   => (float) (clone $summaryBase)->sum('total'),
                'paid'    => (float) (clone $summaryBase)->where('payment_status', 'paid')->sum('total'),
                'pending' => (clone $summaryBase)->where('order_status', 'pending')->count(),
            ],
        ], "orders-{$stamp}.pdf");
    }

    // ── helpers ─────────────────────────────────────────────────

    /** The order list's filters (search, statuses, method, date range) - validated. */
    private function orderFilters(Request $request): array
    {
        $data = $request->validate([
            'q'              => ['nullable', 'string', 'max:100'],
            'order_status'   => ['nullable', Rule::in(Order::STATUSES)],
            'payment_status' => ['nullable', Rule::in(Order::PAYMENT_STATUSES)],
            'payment_method' => ['nullable', 'string', 'max:30'],
            'date_from'      => ['nullable', 'date'],
            'date_to'        => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return array_filter($data, fn ($v) => $v !== null && $v !== '');
    }

    private function filteredOrders(Request $request)
    {
        $f = $this->orderFilters($request);

        return Order::query()
            ->when(isset($f['q']), function ($q) use ($f) {
                $term = '%' . trim($f['q']) . '%';
                $q->where(fn ($w) => $w->where('order_number', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]));
            })
            ->when(isset($f['order_status']), fn ($q) => $q->where('order_status', $f['order_status']))
            ->when(isset($f['payment_status']), fn ($q) => $q->where('payment_status', $f['payment_status']))
            ->when(isset($f['payment_method']), fn ($q) => $q->where('payment_method', $f['payment_method']))
            ->when(isset($f['date_from']), fn ($q) => $q->whereDate('created_at', '>=', $f['date_from']))
            ->when(isset($f['date_to']), fn ($q) => $q->whereDate('created_at', '<=', $f['date_to']));
    }



    private function respond(Request $request, array $result)
    {
        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function badge(string $status): string
    {
        return Order::statusBadge($status);
    }
}
