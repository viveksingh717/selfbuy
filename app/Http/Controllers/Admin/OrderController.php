<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AdminOrderService;
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

        $query = Order::query()
            ->withCount('items')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn ($w) => $w->where('order_number', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term]));
            })
            ->when($request->filled('order_status'), fn ($q) => $q->where('order_status', $request->order_status))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->payment_method))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to));

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

    // ── helpers ─────────────────────────────────────────────────

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
