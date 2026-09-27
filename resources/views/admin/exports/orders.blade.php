@extends('admin.exports.layout')

@section('title', 'Orders')
@section('subtitle', $filterLine)

@php $money = fn ($v) => '₹' . number_format((float) $v, 2); @endphp

@section('content')
    <table class="summary">
        <tr>
            <td><div class="v">{{ number_format($totalCount) }}</div><div class="l">Orders</div></td>
            <td><div class="v">{{ $money($summary['value']) }}</div><div class="l">Order value</div></td>
            <td><div class="v">{{ $money($summary['paid']) }}</div><div class="l">Paid</div></td>
            <td><div class="v">{{ number_format($summary['pending']) }}</div><div class="l">Pending orders</div></td>
        </tr>
    </table>

    @if ($orders->isEmpty())
        <p class="muted">No orders match these filters.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Order</th><th>Date</th><th>Customer</th><th>City</th><th class="c">Items</th>
                    <th class="r">Total</th><th>Payment</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $o)
                    <tr>
                        <td><strong>#{{ $o->order_number }}</strong></td>
                        <td>{{ $o->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $o->customerName() }}<br><span class="muted">{{ $o->email }}</span></td>
                        <td>{{ $o->city }}{{ $o->state ? ', ' . $o->state : '' }}</td>
                        <td class="c">{{ $o->items_count }}</td>
                        <td class="r"><strong>{{ $money($o->total) }}</strong>@if ((float) $o->discount > 0)<br><span class="muted">-{{ $money($o->discount) }} {{ $o->coupon_code }}</span>@endif</td>
                        <td>{{ strtoupper($o->payment_method) }}<br>
                            <span class="pill" style="background: {{ \App\Models\Order::statusColor($o->payment_status) }}">{{ ucfirst($o->payment_status) }}</span></td>
                        <td><span class="pill" style="background: {{ \App\Models\Order::statusColor($o->order_status) }}">{{ ucfirst($o->order_status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($totalCount > $orders->count())
            <p class="note">Showing the latest {{ number_format($orders->count()) }} of {{ number_format($totalCount) }} orders - export CSV for the full list.</p>
        @endif
    @endif
@endsection
