@extends('admin.layouts.app')

@section('title', 'Transaction PAY-' . $payment->id)
@section('subTitle', 'Transaction Details')

@use('App\Services\TransactionHistoryService', 'Tx')

@php
    $order = $payment->order;
    $status = $order && $order->payment_status === 'refunded' && $payment->status === 'paid'
        ? 'refunded' : ($payment->status === 'created' ? 'pending' : $payment->status);
    $money = fn ($v) => Tx::money((float) $v, $payment->currency);
    // Never show secrets that can sit in billing_data (checkout "create account" password hash).
    $billing = collect($payment->billing_data ?? [])->except(['account_password_hash', 'account_password_encrypted', 'account_password']);
    $snapshotItems = $payment->order_snapshot['items'] ?? [];
    $snapshotTotals = $payment->order_snapshot['totals'] ?? [];
@endphp

@section('style')
    <style>
        .tx-meta dt { font-weight: 500; color: #9aa0ac; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; }
        .tx-meta dd { margin-bottom: 12px; word-break: break-word; }
        .tx-raw { max-height: 360px; overflow: auto; background: #f4f5f8; border-radius: 4px; padding: 12px; font-size: 12px; }
    </style>
@endsection

@section('content')
<div class="section-body mt-3">
    <div class="container-fluid">
        <div class="mb-3"><a href="{{ route('admin.transactions') }}" class="text-muted"><i class="fa fa-arrow-left"></i> Back to transactions</a></div>

        <div class="row clearfix">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex justify-content-between align-items-start">
                            <div class="mb-2">
                                <h4 class="mb-1">PAY-{{ $payment->id }}</h4>
                                <div class="text-muted small">{{ Tx::gatewayLabel($payment->gateway) }} &middot; {{ $payment->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="text-md-right">
                                <div style="font-size:24px;font-weight:600">{{ $money($payment->amount) }}</div>
                                <span class="tag" style="background: {{ Tx::statusColor($status) }}; color:#fff">{{ ucfirst($status) }}</span>
                            </div>
                        </div>
                        @if ($payment->failure_reason)
                            <div class="alert alert-danger mb-0 mt-3"><strong>Failure reason:</strong> {{ $payment->failure_reason }}</div>
                        @endif
                        @if ($status === 'pending')
                            <div class="alert alert-warning mb-0 mt-3">This payment was started but never completed - the customer may have closed the payment window. No money was taken.</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Gateway details</h3></div>
                    <div class="card-body">
                        <dl class="tx-meta row mb-0">
                            <div class="col-sm-6"><dt>Gateway</dt><dd>{{ Tx::gatewayLabel($payment->gateway) }}</dd></div>
                            <div class="col-sm-6"><dt>Amount</dt><dd>{{ $money($payment->amount) }} {{ $payment->currency }}</dd></div>
                            <div class="col-sm-6"><dt>Gateway payment ID</dt><dd>{{ $payment->gateway_payment_id ?: '-' }}</dd></div>
                            <div class="col-sm-6"><dt>Gateway order / session ID</dt><dd>{{ $payment->gateway_order_id ?: '-' }}</dd></div>
                            <div class="col-sm-6"><dt>Started</dt><dd>{{ $payment->created_at->format('d M Y, h:i:s A') }}</dd></div>
                            <div class="col-sm-6"><dt>Paid</dt><dd>{{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i:s A') : '-' }}</dd></div>
                        </dl>
                        @if (!empty($payment->meta))
                            <details class="mt-2">
                                <summary class="text-muted" style="cursor:pointer">Raw gateway response</summary>
                                <pre class="tx-raw mt-2 mb-0">{{ json_encode($payment->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                        @endif
                    </div>
                </div>

                @if (!$order && !empty($snapshotItems))
                    {{-- No order was created (payment not completed) - show what the customer was buying --}}
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Cart at the time of payment</h3></div>
                        <div class="table-responsive">
                            <table class="table table-vcenter mb-0">
                                <thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-right">Total</th></tr></thead>
                                <tbody>
                                    @foreach ($snapshotItems as $item)
                                        <tr>
                                            <td>{{ $item['product_name'] ?? 'Item' }}@if (!empty($item['variant_label']))<div class="text-muted small">{{ $item['variant_label'] }}</div>@endif</td>
                                            <td class="text-center">{{ $item['qty'] ?? 1 }}</td>
                                            <td class="text-right">₹{{ number_format((float) ($item['line_total'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if (isset($snapshotTotals['cart_total']) || isset($snapshotTotals['total']))
                                    <tfoot><tr><td colspan="2" class="text-right font-weight-bold">Cart total</td>
                                        <td class="text-right font-weight-bold">₹{{ number_format((float) ($snapshotTotals['cart_total'] ?? $snapshotTotals['total']), 2) }}</td></tr></tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Order</h3></div>
                    <div class="card-body">
                        @if ($order)
                            <dl class="tx-meta mb-3">
                                <dt>Order</dt><dd>#{{ $order->order_number }}</dd>
                                <dt>Order status</dt><dd>{!! \App\Models\Order::statusBadge($order->order_status) !!}</dd>
                                <dt>Order total</dt><dd>₹{{ number_format((float) $order->total, 2) }}</dd>
                            </dl>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary btn-block"><i class="fa fa-shopping-cart"></i> Open order</a>
                        @else
                            <p class="text-muted mb-0">No order was created - the payment {{ $status === 'failed' ? 'failed' : 'was not completed' }}, so the customer's cart was not turned into an order.</p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Customer</h3></div>
                    <div class="card-body">
                        @php
                            $name = $order ? $order->customerName() : trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
                            $email = $order->email ?? ($billing['email'] ?? null);
                            $phone = $order->phone ?? ($billing['phone'] ?? null);
                        @endphp
                        <dl class="tx-meta mb-0">
                            <dt>Name</dt><dd>{{ $name ?: '-' }}</dd>
                            <dt>Email</dt><dd>{!! $email ? '<a href="mailto:' . e($email) . '">' . e($email) . '</a>' : '-' !!}</dd>
                            <dt>Phone</dt><dd>{{ $phone ?: '-' }}</dd>
                            @if ($billing->has('address_line1'))
                                <dt>Billing address</dt>
                                <dd>{{ $billing['address_line1'] }}@if (!empty($billing['address_line2'])), {{ $billing['address_line2'] }}@endif<br>
                                    {{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['postal_code'] ?? '' }}<br>{{ $billing['country'] ?? '' }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
