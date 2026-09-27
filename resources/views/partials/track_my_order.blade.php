@extends('layouts.insight')
@section('title', 'Track My Order')
@section('subTitle', 'Track My Order')

@php
    $money = fn ($v) => '₹' . number_format((float) $v, 2);
    $searched = request('order');
    $notFound = $searched && (!$selected || $selected->order_number !== $searched);
@endphp

@section('style')
    <style>
        .tmo-card { border: 1px solid #ebebeb; border-radius: 6px; padding: 2rem; margin-bottom: 2rem; background: #fff; }
        .tmo-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 1rem; margin-bottom: 2rem; }
        .tmo-head h3 { font-size: 2rem; margin-bottom: .3rem; }
        .tmo-pill { display: inline-block; padding: .2rem 1rem; border-radius: 2rem; color: #fff; font-size: 1.2rem; font-weight: 500; }
        .tmo-ship { background: #f9f9f9; border-radius: 6px; padding: 1.5rem 2rem; margin-top: 2rem; }
        .tmo-items td { padding: 1rem .5rem; vertical-align: top; border-top: 1px solid #ebebeb; }
        .tmo-items tr:first-child td { border-top: 0; }
        .tmo-list a { display: block; border: 1px solid #ebebeb; border-radius: 6px; padding: 1.2rem 1.5rem; margin-bottom: 1rem; color: inherit; transition: border-color .15s; }
        .tmo-list a:hover, .tmo-list a.active { border-color: #c96; }
        .tmo-list .num { font-weight: 600; color: #333; }
    </style>
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
            <div class="container">
                <h1 class="page-title">Track My Order<span>Follow every step of your delivery</span></h1>
            </div>
        </div>
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('myaccount') }}">My Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Track My Order</li>
                </ol>
            </div>
        </nav>

        <div class="page-content">
            <div class="container">
                @if ($orders->isEmpty())
                    <div class="text-center py-5">
                        <h3>No orders to track yet</h3>
                        <p>When you place an order, you can follow its journey here.</p>
                        <a href="{{ route('products') }}" class="btn btn-outline-primary-2"><span>START SHOPPING</span><i class="icon-long-arrow-right"></i></a>
                    </div>
                @else
                    <div class="row">
                        {{-- ══════ Selected order ══════ --}}
                        <div class="col-lg-8">
                            @if ($notFound)
                                <div class="alert alert-warning">
                                    We couldn't find order <strong>{{ $searched }}</strong> in your account. Check the number, or open it
                                    with the "View your order" link in its confirmation email if it was placed with a different account.
                                </div>
                            @endif

                            <div class="tmo-card">
                                <div class="tmo-head">
                                    <div>
                                        <h3>Order #{{ $selected->order_number }}</h3>
                                        <div class="text-muted">
                                            Placed {{ $selected->created_at->format('d M Y') }}
                                            &middot; {{ $selected->items->sum('qty') }} {{ Str::plural('item', $selected->items->sum('qty')) }}
                                            &middot; {{ $money($selected->total) }}
                                        </div>
                                    </div>
                                    <div class="text-md-right">
                                        <span class="tmo-pill" style="background: {{ \App\Models\Order::statusColor($selected->order_status) }}">{{ ucfirst($selected->order_status) }}</span>
                                        <div class="mt-1"><small class="text-muted">Payment: {{ ucfirst($selected->payment_status) }} ({{ $selected->paymentMethodLabel() }})</small></div>
                                    </div>
                                </div>

                                @include('partials.order_journey', ['order' => $selected])

                                @if ($selected->order_status !== 'cancelled')
                                    <div class="tmo-ship">
                                        @if ($selected->tracking_number)
                                            <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:1rem">
                                                <div>
                                                    <strong>Shipment</strong><br>
                                                    {{ $selected->tracking_courier ?: 'Courier' }} &middot; Tracking no. <strong>{{ $selected->tracking_number }}</strong>
                                                </div>
                                                @if ($selected->tracking_url)
                                                    <a href="{{ $selected->tracking_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary-2 btn-sm"><span>TRACK PACKAGE</span><i class="icon-long-arrow-right"></i></a>
                                                @endif
                                            </div>
                                        @endif

                                        <p class="mb-0 {{ $selected->tracking_number ? 'mt-1' : '' }} text-muted">
                                            @switch($selected->order_status)
                                                @case('pending')    We've received your order and will confirm it shortly. @break
                                                @case('processing') Your items are being packed. You'll get an email with tracking details as soon as it ships. @break
                                                @case('shipped')    On its way! Orders usually arrive within {{ setting('delivery_time_estimate', '3 - 7 business days') }} of shipping. @break
                                                @case('delivered')  Delivered {{ optional($selected->delivered_at)->format('d M Y') }}. We hope you love it! @break
                                            @endswitch
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="tmo-card">
                                <h4 class="mb-2">Items in this order</h4>
                                <table class="w-100 tmo-items">
                                    @foreach ($selected->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product_name }}
                                                @if ($item->variant_label)<br><small class="text-muted">{{ $item->variant_label }}</small>@endif
                                            </td>
                                            <td class="text-center text-muted" style="width:60px">&times;{{ $item->qty }}</td>
                                            <td class="text-right" style="width:120px">{{ $money($item->line_total) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                                <div class="d-flex justify-content-between border-top pt-2 mt-1">
                                    <strong>Total</strong><strong>{{ $money($selected->total) }}</strong>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center mt-3" style="gap:1rem">
                                    <div class="d-flex flex-wrap" style="gap:8px">
                                        <a href="{{ route('checkout.success', $selected->order_number) }}" class="btn btn-outline-primary-2 btn-sm"><span>VIEW FULL ORDER DETAILS</span></a>
                                        <a href="{{ route('order.invoice', $selected->order_number) }}" class="btn btn-outline-primary-2 btn-sm"><i class="icon-long-arrow-down"></i><span>INVOICE</span></a>
                                    </div>
                                    <small class="text-muted">Question about this order? <a href="{{ route('contact') }}">Contact us</a></small>
                                </div>
                            </div>
                        </div>

                        {{-- ══════ Search + all orders ══════ --}}
                        <aside class="col-lg-4">
                            <form method="GET" action="{{ route('track_order') }}" class="mb-3">
                                <label for="tmo-search">Find an order</label>
                                <div class="input-group">
                                    <input type="text" id="tmo-search" name="order" class="form-control" maxlength="40"
                                        placeholder="Order number, e.g. ORD26..." value="{{ $searched }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary-2" aria-label="Find order"><i class="icon-search"></i></button>
                                    </div>
                                </div>
                            </form>

                            <h4 class="mb-2">Your orders ({{ $orders->count() }})</h4>
                            <div class="tmo-list">
                                @foreach ($orders as $o)
                                    <a href="{{ route('track_order', ['order' => $o->order_number]) }}" class="{{ $o->is($selected) ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="num">#{{ $o->order_number }}</span>
                                            <span class="tmo-pill" style="background: {{ \App\Models\Order::statusColor($o->order_status) }}">{{ ucfirst($o->order_status) }}</span>
                                        </div>
                                        <small class="text-muted">{{ $o->created_at->format('d M Y') }} &middot; {{ $money($o->total) }}</small>
                                    </a>
                                @endforeach
                            </div>
                        </aside>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
