<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ExportService;
use App\Services\TransactionHistoryService as Tx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    public function __construct(private Tx $transactions)
    {
    }

    /** Transaction History page, or its DataTables JSON (with the filter bar's values). */
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        if (!$request->ajax()) {
            return view('admin.transactions.index', [
                'summary' => $this->transactions->summary(),
            ]);
        }

        return DataTables::of($this->transactions->query($filters))
            ->editColumn('created_at', fn ($t) => '<span title="' . e($t->created_at) . '">' . date('d M Y', strtotime($t->created_at)) . '</span>'
                . '<div class="text-muted small">' . date('h:i A', strtotime($t->created_at)) . '</div>')
            ->addColumn('transaction', function ($t) {
                // The ID is the link: online payments open their detail page, COD rows open the order.
                $label = $t->source === 'cod' ? 'COD-' . $t->order_id : 'PAY-' . $t->payment_id;
                $url = $t->payment_id ? route('admin.transactions.show', $t->payment_id) : route('admin.orders.show', $t->order_id);
                $ref = $t->reference ? '<div class="text-muted text-break" style="font-size:12px">' . e($t->reference) . '</div>' : '';

                return '<a href="' . $url . '" class="font-weight-bold">' . $label . '</a>' . $ref;
            })
            ->editColumn('order_number', fn ($t) => $t->order_id
                ? '<a href="' . route('admin.orders.show', $t->order_id) . '">#' . e($t->order_number) . '</a>'
                : '<span class="text-muted small">No order<br>(payment not completed)</span>')
            ->addColumn('customer', fn ($t) => '<div>' . e(trim((string) $t->customer_name) ?: '-') . '</div><div class="text-muted small">' . e($t->customer_email) . '</div>')
            ->editColumn('gateway', fn ($t) => e(Tx::gatewayLabel($t->gateway)))
            ->editColumn('amount', fn ($t) => '<span class="font-weight-bold">' . Tx::money((float) $t->amount, $t->currency) . '</span>')
            ->editColumn('status', fn ($t) => '<span class="tag" style="background:' . Tx::statusColor($t->status) . ';color:#fff">' . ucfirst($t->status) . '</span>'
                . ($t->note ? '<div class="text-muted" style="font-size:12px;max-width:180px;white-space:normal">' . e($t->note) . '</div>' : ''))
            ->filterColumn('customer', fn ($q, $kw) => $q->where('customer_name', 'like', "%{$kw}%"))
            ->orderColumn('customer', 'customer_name $1')
            ->orderColumn('transaction', 'uid $1')
            ->rawColumns(['created_at', 'transaction', 'order_number', 'customer', 'amount', 'status'])
            ->make(true);
    }

    /** Full detail of one online payment attempt (COD rows link to their order instead). */
    public function show($id)
    {
        $payment = Payment::with('order')->findOrFail($id);

        return view('admin.transactions.show', compact('payment'));
    }

    /** CSV or PDF of the current filtered list - for accounting / reconciliation. */
    public function export(Request $request, ExportService $export, string $format = 'csv')
    {
        abort_unless(in_array($format, ['csv', 'pdf'], true), 404);

        $filters = $this->filters($request);
        // uid tie-breaker keeps chunked pages stable when timestamps are equal
        $rows = $this->transactions->query($filters)->orderByDesc('created_at')->orderBy('uid');
        $stamp = now()->format('Y-m-d-His');

        if ($format === 'csv') {
            return $export->csv("transactions-{$stamp}.csv", function ($put) use ($rows) {
                $put(['Date', 'Transaction', 'Gateway reference', 'Order', 'Customer', 'Email', 'Method', 'Amount', 'Currency', 'Status', 'Paid at', 'Note']);
                $rows->chunk(500, function ($chunk) use ($put) {
                    foreach ($chunk as $t) {
                        $put([
                            $t->created_at,
                            $t->source === 'cod' ? 'COD-' . $t->order_id : 'PAY-' . $t->payment_id,
                            $t->reference,
                            $t->order_number,
                            trim((string) $t->customer_name),
                            $t->customer_email,
                            Tx::gatewayLabel($t->gateway),
                            number_format((float) $t->amount, 2, '.', ''),
                            $t->currency,
                            $t->status,
                            $t->paid_at,
                            $t->note,
                        ]);
                    }
                });
            });
        }

        return $export->pdf('admin.exports.transactions', [
            'rows'       => (clone $rows)->limit(ExportService::PDF_ROW_LIMIT)->get(),
            'totalCount' => DB::query()->fromSub($this->transactions->query($filters), 'c')->count(),
            'summary'    => $this->transactions->summary($filters),
            'filterLine' => ExportService::describeFilters($filters, ['q' => 'Search', 'gateway' => 'Method', 'date_from' => 'From', 'date_to' => 'To']),
        ], "transactions-{$stamp}.pdf");
    }

    private function filters(Request $request): array
    {
        $data = $request->validate([
            'q'         => ['nullable', 'string', 'max:100'],
            'gateway'   => ['nullable', 'in:' . implode(',', Tx::GATEWAYS)],
            'status'    => ['nullable', 'in:' . implode(',', Tx::STATUSES)],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return array_filter($data, fn ($v) => $v !== null && $v !== '');
    }
}
