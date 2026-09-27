@extends('admin.exports.layout')

@use('App\Services\TransactionHistoryService', 'Tx')

@section('title', 'Transaction History')
@section('subtitle', $filterLine)

@php $by = $summary['byStatus']; @endphp

@section('content')
    <table class="summary">
        <tr>
            <td><div class="v">{{ Tx::moneyList($by['paid']['amounts']) }}</div><div class="l">Collected ({{ $by['paid']['count'] }})</div></td>
            <td><div class="v">{{ Tx::moneyList($by['pending']['amounts']) }}</div><div class="l">Awaiting ({{ $by['pending']['count'] }})</div></td>
            <td><div class="v">{{ Tx::moneyList($by['failed']['amounts']) }}</div><div class="l">Failed ({{ $by['failed']['count'] }})</div></td>
            <td><div class="v">{{ Tx::moneyList($by['refunded']['amounts']) }}</div><div class="l">Refunded ({{ $by['refunded']['count'] }})</div></td>
        </tr>
    </table>

    @if ($rows->isEmpty())
        <p class="muted">No transactions match these filters.</p>
    @else
        <table class="data">
            <thead>
                <tr><th>Date</th><th>Transaction</th><th>Order</th><th>Customer</th><th>Method</th><th class="r">Amount</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach ($rows as $t)
                    <tr>
                        <td>{{ date('d M Y, h:i A', strtotime($t->created_at)) }}</td>
                        <td><strong>{{ $t->source === 'cod' ? 'COD-' . $t->order_id : 'PAY-' . $t->payment_id }}</strong>@if ($t->reference)<br><span class="muted">{{ $t->reference }}</span>@endif</td>
                        <td>{{ $t->order_number ? '#' . $t->order_number : 'No order' }}</td>
                        <td>{{ trim((string) $t->customer_name) ?: '-' }}<br><span class="muted">{{ $t->customer_email }}</span></td>
                        <td>{{ Tx::gatewayLabel($t->gateway) }}</td>
                        <td class="r"><strong>{{ Tx::money((float) $t->amount, $t->currency) }}</strong></td>
                        <td><span class="pill" style="background: {{ Tx::statusColor($t->status) }}">{{ ucfirst($t->status) }}</span>@if ($t->note)<br><span class="muted">{{ $t->note }}</span>@endif</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($totalCount > $rows->count())
            <p class="note">Showing the latest {{ number_format($rows->count()) }} of {{ number_format($totalCount) }} transactions - export CSV for the full list.</p>
        @endif
    @endif
@endsection
