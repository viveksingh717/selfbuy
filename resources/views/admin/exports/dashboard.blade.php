@extends('admin.exports.layout')

@use('App\Models\Order')
@use('App\Services\TransactionHistoryService', 'Tx')

@section('title', 'Store Report')
@section('subtitle', $periodLabel . ' (' . $from->format('d M Y') . ' – ' . $to->format('d M Y') . ')')

@php
    $inr = fn ($v) => '₹' . number_format((float) $v, 2);
    $change = fn ($c) => $c === null ? 'new' : (($c > 0 ? '+' : '') . $c . '%');
    $k = $kpis;
    $activeSales = collect($sales['labels'])->map(fn ($l, $i) => [$l, $sales['orders'][$i], $sales['revenue'][$i]])->filter(fn ($r) => $r[1] > 0);
@endphp

@section('content')
    <table class="summary">
        <tr>
            <td><div class="v">{{ $inr($k['revenue']['value']) }}</div><div class="l">Revenue (paid) · {{ $change($k['revenue']['change']) }}</div></td>
            <td><div class="v">{{ number_format($k['orders']['value']) }}</div><div class="l">Orders · {{ $change($k['orders']['change']) }}</div></td>
            <td><div class="v">{{ $inr($k['aov']['value']) }}</div><div class="l">Avg. order value · {{ $change($k['aov']['change']) }}</div></td>
            <td><div class="v">{{ number_format($k['customers']['value']) }}</div><div class="l">Customers · {{ $change($k['customers']['change']) }}</div></td>
        </tr>
    </table>
    <p class="muted" style="margin:0">Changes compare with the previous period of the same length. {{ $k['cancelled'] }} cancelled {{ Str::plural('order', $k['cancelled']) }} · {{ $k['new_accounts'] }} new customer {{ Str::plural('account', $k['new_accounts']) }}.</p>

    <table style="width:100%; margin-top:6px">
        <tr>
            <td style="width:48%; vertical-align:top; padding-right:10px">
                <h2>Orders by status</h2>
                <table class="data">
                    <thead><tr><th>Status</th><th class="r">Orders</th></tr></thead>
                    <tbody>
                        @foreach ($statuses as $status => $n)
                            <tr><td><span class="pill" style="background: {{ Order::statusColor($status) }}">{{ ucfirst($status) }}</span></td><td class="r">{{ $n }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width:52%; vertical-align:top">
                <h2>Payment methods (excl. cancelled)</h2>
                <table class="data">
                    <thead><tr><th>Method</th><th class="r">Orders</th><th class="r">Value</th></tr></thead>
                    <tbody>
                        @forelse ($paymentMix as $m)
                            <tr><td>{{ Tx::gatewayLabel($m->payment_method) }}</td><td class="r">{{ $m->orders }}</td><td class="r">{{ $inr($m->value) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="muted">No orders in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <h2>Top selling products</h2>
    <table class="data">
        <thead><tr><th>#</th><th>Product</th><th class="c">Units</th><th class="c">Orders</th><th class="r">Revenue</th></tr></thead>
        <tbody>
            @forelse ($topProducts as $i => $p)
                <tr><td>{{ $i + 1 }}</td><td>{{ $p->product_name }}</td><td class="c">{{ $p->units }}</td><td class="c">{{ $p->orders }}</td><td class="r">{{ $inr($p->revenue) }}</td></tr>
            @empty
                <tr><td colspan="5" class="muted">No sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($activeSales->isNotEmpty())
        <h2>Sales by date</h2>
        <table class="data">
            <thead><tr><th>Date</th><th class="c">Orders</th><th class="r">Revenue (paid)</th></tr></thead>
            <tbody>
                @foreach ($activeSales as [$label, $n, $rev])
                    <tr><td>{{ $label }}</td><td class="c">{{ $n }}</td><td class="r">{{ $inr($rev) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Orders in this period ({{ number_format($orderCount) }})</h2>
    @if ($orders->isEmpty())
        <p class="muted">No orders in this period.</p>
    @else
        <table class="data">
            <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th class="r">Total</th><th>Payment</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($orders as $o)
                    <tr>
                        <td>#{{ $o->order_number }}</td>
                        <td>{{ $o->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $o->customerName() }}<br><span class="muted">{{ $o->email }}</span></td>
                        <td class="r">{{ $inr($o->total) }}</td>
                        <td>{{ strtoupper($o->payment_method) }} · {{ ucfirst($o->payment_status) }}</td>
                        <td><span class="pill" style="background: {{ Order::statusColor($o->order_status) }}">{{ ucfirst($o->order_status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($orderCount > $orders->count())
            <p class="note">Showing the latest {{ number_format($orders->count()) }} of {{ number_format($orderCount) }} orders - use the CSV report for the full list.</p>
        @endif
    @endif
@endsection
