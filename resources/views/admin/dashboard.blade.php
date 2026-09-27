@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('subTitle', 'Dashboard')

@use('App\Models\Order')
@use('App\Services\DashboardService')
@use('App\Services\TransactionHistoryService', 'Tx')

@php
    $inr = fn ($v) => '₹' . number_format((float) $v, 0);
    $kpiCards = [
        ['revenue',   'Revenue',          fn ($v) => $inr($v), 'fa-inr',          '#21ba45', 'Paid orders'],
        ['orders',    'Orders',           fn ($v) => number_format($v), 'fa-shopping-cart', '#2185d0', $kpis['cancelled'] . ' cancelled'],
        ['aov',       'Avg. order value', fn ($v) => $inr($v), 'fa-line-chart',   '#6435c9', 'Per paid order'],
        ['customers', 'Customers',        fn ($v) => number_format($v), 'fa-users', '#f2a007', $kpis['new_accounts'] . ' new accounts'],
    ];
    $totalOrdersAll = array_sum($statuses);
@endphp

@section('style')
    <style>
        .dash-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; }
        .dash-head h4 { margin: 0; }
        .dash-range .btn { border-radius: 20px; padding: 4px 14px; font-size: 13px; }
        .kpi { background: #fff; border-radius: 8px; padding: 16px 18px; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,.06); position: relative; }
        .kpi .kpi-icon { position: absolute; right: 16px; top: 16px; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; }
        .kpi .kpi-label { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #9aa0ac; padding-right: 46px; }
        .kpi .kpi-value { font-size: 26px; font-weight: 700; margin: 4px 0 6px; line-height: 1.2; }
        .kpi .kpi-sub { font-size: 12px; color: #6e7687; }
        .kpi-change { display: inline-block; font-size: 12px; font-weight: 600; padding: 1px 7px; border-radius: 10px; margin-right: 6px; }
        .kpi-change.up { background: rgba(33,186,69,.12); color: #1a9338; }
        .kpi-change.down { background: rgba(219,40,40,.12); color: #c21f1f; }
        .kpi-change.flat { background: #f0f2f5; color: #6e7687; }
        .ov-title { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #9aa0ac; font-weight: 600; margin: 4px 0 10px; }
        .ov-tile { display: flex; align-items: center; background: #fff; border-radius: 8px; padding: 14px; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,.06); color: inherit; transition: box-shadow .15s; }
        .ov-tile:hover { text-decoration: none; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
        .ov-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 17px; margin-right: 12px; flex: 0 0 42px; }
        .ov-num { font-size: 21px; font-weight: 700; line-height: 1.15; }
        .ov-lbl { font-size: 12px; color: #6e7687; }
        .ov-sub { font-size: 11px; color: #9aa0ac; }
        .live-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #21ba45; margin-right: 4px; box-shadow: 0 0 0 3px rgba(33,186,69,.2); animation: live 1.6s infinite; }
        @keyframes live { 50% { box-shadow: 0 0 0 6px rgba(33,186,69,.05); } }
        .top-tabs .nav-link { padding: 2px 10px; font-size: 12px; border-radius: 12px; color: #6e7687; }
        .top-tabs .nav-link.active { background: #2185d0; color: #fff; }
        .dash-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,.06); margin-bottom: 20px; }
        .dash-card .dc-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f0f2f5; }
        .dash-card .dc-head h3 { font-size: 15px; font-weight: 600; margin: 0; }
        .dash-card .dc-body { padding: 16px 18px; }
        .attn-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f4f5f8; color: inherit; }
        .attn-item:last-child { border-bottom: 0; }
        .attn-item:hover { text-decoration: none; background: #fafbfc; }
        .attn-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; margin-right: 12px; flex: 0 0 34px; }
        .attn-count { margin-left: auto; font-weight: 700; font-size: 16px; }
        .attn-count.zero { color: #c5cad3; font-weight: 500; }
        .status-row { display: flex; align-items: center; padding: 6px 0; color: inherit; font-size: 13px; }
        .status-row:hover { text-decoration: none; }
        .status-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; }
        .status-row .n { margin-left: auto; font-weight: 600; }
        .dash-table td, .dash-table th { vertical-align: middle; padding: 10px 8px; }
        .dash-table .cust { max-width: 190px; }
        .dash-table .cust .small { word-break: break-all; }   /* long emails wrap instead of widening the table */
        .dash-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #9aa0ac; border-top: 0; }
        .top-img { width: 38px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #eef0f3; margin-right: 10px; }
        .mix-bar { height: 8px; border-radius: 4px; background: #f0f2f5; overflow: hidden; }
        .mix-bar span { display: block; height: 100%; border-radius: 4px; }
        .empty-note { color: #9aa0ac; text-align: center; padding: 24px 0; font-size: 13px; }
        @media (max-width: 575.98px) {
            .kpi .kpi-value { font-size: 20px; }
            .kpi .kpi-icon { width: 30px; height: 30px; font-size: 13px; right: 12px; top: 12px; }
            .kpi .kpi-label { padding-right: 36px; font-size: 11px; }
            .dash-table .cust { min-width: 170px; }                 /* table scrolls sideways instead */
            .dash-table .cust .small { word-break: normal; }
        }
    </style>
@endsection

@section('content')
<div class="section-body mt-3">
    <div class="container-fluid">

        {{-- ── Header + period ── --}}
        <div class="dash-head">
            <div>
                <h4>Welcome, {{ $adminDetails->name }}!</h4>
                <div class="text-muted">
                    Here's what's happening in your store today.
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="ml-1">Go to store <i class="fa fa-external-link"></i></a>
                </div>
            </div>
            <div class="text-lg-right">
                <div class="d-flex flex-wrap justify-content-lg-end align-items-center" style="gap:8px">
                    <div class="dash-range btn-group flex-wrap" role="group" aria-label="Period">
                        @foreach (DashboardService::RANGES as $key => $label)
                            <a href="{{ route('admin.dashboard', ['range' => $key]) }}" class="btn btn-sm {{ $range === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                        @endforeach
                        <button type="button" class="btn btn-sm {{ $range === 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}" id="customToggle">
                            <i class="fa fa-calendar"></i> Custom
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="border-radius:20px">
                            <i class="fa fa-download"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('admin.dashboard.export', ['format' => 'csv'] + request()->only(['range', 'from', 'to'])) }}"><i class="fa fa-file-excel-o text-success"></i> CSV report</a>
                            <a class="dropdown-item" href="{{ route('admin.dashboard.export', ['format' => 'pdf'] + request()->only(['range', 'from', 'to'])) }}"><i class="fa fa-file-pdf-o text-danger"></i> PDF report</a>
                        </div>
                    </div>
                </div>

                {{-- Custom date range --}}
                <form method="GET" action="{{ route('admin.dashboard') }}" id="customRange"
                    class="flex-wrap justify-content-lg-end align-items-center mt-2 {{ $range === 'custom' ? 'd-flex' : 'd-none' }}" style="gap:6px">
                    <input type="hidden" name="range" value="custom">
                    <input type="date" name="from" class="form-control form-control-sm" style="width:auto" required max="{{ now()->toDateString() }}"
                        value="{{ $range === 'custom' ? $from->toDateString() : now()->subDays(29)->toDateString() }}" aria-label="From date">
                    <span class="text-muted small">to</span>
                    <input type="date" name="to" class="form-control form-control-sm" style="width:auto" required max="{{ now()->toDateString() }}"
                        value="{{ $range === 'custom' ? $to->toDateString() : now()->toDateString() }}" aria-label="To date">
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                </form>

                {{-- Which dates the "performance" cards and charts below cover --}}
                <div class="text-muted small mt-1">
                    Showing {{ $from->isSameDay($to) ? ($from->isToday() ? 'today, ' : '') . $to->format('d M Y') : $from->format('d M Y') . ' – ' . $to->format('d M Y') }}
                </div>
            </div>
        </div>

        {{-- ── Store overview (all time) ── --}}
        @php
            $ov = $overview;
            $tiles = [
                ['Total orders', number_format($ov['orders']), null, 'fa-shopping-bag', '#2185d0', route('admin.orders')],
                ['Pending orders', number_format($ov['pending']), 'waiting to be confirmed', 'fa-hourglass-half', '#f2a007', route('admin.orders', ['order_status' => 'pending'])],
                ['Total revenue', $inr($ov['revenue']), 'paid orders', 'fa-inr', '#21ba45', route('admin.transactions', ['status' => 'paid'])],
                ['Customers', number_format($ov['customers']), '+ ' . $ov['guest_buyers'] . ' guest ' . Str::plural('buyer', $ov['guest_buyers']) . ($ov['new_today'] ? ' · ' . $ov['new_today'] . ' new today' : ''), 'fa-users', '#6435c9', null],
                ['Complaints / issues', number_format($ov['complaints_open']), 'open of ' . $ov['complaints'] . ' total', 'fa-life-ring', '#db2828', route('admin.contact_us')],
            ];
        @endphp
        <div class="ov-title">Store overview &middot; all time</div>
        <div class="row clearfix">
            @foreach ($tiles as [$label, $value, $sub, $icon, $color, $url])
                <div class="col-6 col-md-4 col-xl mb-3">
                    <{{ $url ? 'a href=' . $url : 'div' }} class="ov-tile">
                        <span class="ov-icon" style="background: {{ $color }}1f; color: {{ $color }}"><i class="fa {{ $icon }}"></i></span>
                        <div class="min-w-0">
                            <div class="ov-num">{{ $value }}</div>
                            <div class="ov-lbl">
                                {{ $label }}
                            </div>
                            @if ($sub)<div class="ov-sub">{{ $sub }}</div>@endif
                        </div>
                    </{{ $url ? 'a' : 'div' }}>
                </div>
            @endforeach
        </div>

        <div class="ov-title mt-1">{{ $periodLabel }} performance</div>
        {{-- ── KPIs ── --}}
        <div class="row clearfix">
            @foreach ($kpiCards as [$key, $label, $format, $icon, $color, $sub])
                @php $k = $kpis[$key]; @endphp
                <div class="col-6 col-lg-3 mb-3">
                    <div class="kpi">
                        <span class="kpi-icon" style="background: {{ $color }}"><i class="fa {{ $icon }}"></i></span>
                        <div class="kpi-label">{{ $label }}</div>
                        <div class="kpi-value">{{ $format($k['value']) }}</div>
                        <div class="kpi-sub">
                            @if ($k['change'] === null)
                                <span class="kpi-change flat" title="No data for the previous period">new</span>
                            @else
                                <span class="kpi-change {{ $k['change'] > 0 ? 'up' : ($k['change'] < 0 ? 'down' : 'flat') }}">
                                    {{ $k['change'] > 0 ? '▲' : ($k['change'] < 0 ? '▼' : '') }} {{ abs($k['change']) }}%
                                </span>
                            @endif
                            <span class="d-none d-sm-inline">vs previous &middot; </span>{{ $sub }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Sales chart + order pipeline ── --}}
        <div class="row clearfix">
            <div class="col-lg-8">
                <div class="dash-card">
                    <div class="dc-head"><h3>Sales overview</h3><span class="text-muted small">Revenue (paid) and orders &middot; {{ $periodLabel }}</span></div>
                    <div class="dc-body">
                        @if (array_sum($sales['orders']) === 0)
                            <div class="empty-note"><i class="fa fa-bar-chart fa-2x d-block mb-2"></i>No orders in this period yet.</div>
                        @else
                            <div id="salesChart" style="min-height:300px"></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="dash-card">
                    <div class="dc-head"><h3>Order status</h3><a href="{{ route('admin.orders') }}" class="small">All orders ({{ $totalOrdersAll }})</a></div>
                    <div class="dc-body">
                        @if ($totalOrdersAll === 0)
                            <div class="empty-note">No orders yet.</div>
                        @else
                            <div id="statusChart" style="min-height:190px"></div>
                            @foreach ($statuses as $status => $n)
                                <a class="status-row" href="{{ route('admin.orders', ['order_status' => $status]) }}">
                                    <span class="status-dot" style="background: {{ Order::statusColor($status) }}"></span>{{ ucfirst($status) }}
                                    <span class="n">{{ $n }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Needs attention + recent orders ── --}}
        <div class="row clearfix">
            <div class="col-lg-4">
                <div class="dash-card">
                    <div class="dc-head"><h3>Needs attention</h3></div>
                    <div class="dc-body py-1">
                        @foreach ($attention as [$label, $count, $icon, $color, $url])
                            <a class="attn-item" href="{{ $url }}">
                                <span class="attn-icon" style="background: {{ $count ? $color : '#c5cad3' }}"><i class="fa {{ $icon }}"></i></span>
                                <span>{{ $label }}</span>
                                <span class="attn-count {{ $count ? '' : 'zero' }}" style="{{ $count ? 'color:' . $color : '' }}">{{ $count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="dash-card">
                    <div class="dc-head"><h3>Recent orders</h3><a href="{{ route('admin.orders') }}" class="small">View all</a></div>
                    @if ($recentOrders->isEmpty())
                        <div class="empty-note">No orders yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table dash-table mb-0">
                                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach ($recentOrders as $o)
                                        <tr>
                                            <td><a href="{{ route('admin.orders.show', $o->id) }}" class="font-weight-bold">#{{ $o->order_number }}</a>
                                                <div class="text-muted small" title="{{ $o->created_at->format('d M Y, h:i A') }}">{{ $o->created_at->diffForHumans() }} &middot; {{ $o->items_count }} {{ Str::plural('item', $o->items_count) }}</div></td>
                                            <td class="cust">{{ $o->customerName() }}<div class="text-muted small">{{ $o->email }}</div></td>
                                            <td class="text-nowrap font-weight-bold">₹{{ number_format((float) $o->total, 2) }}</td>
                                            <td><div class="small text-muted">{{ strtoupper($o->payment_method) }}</div>{!! Order::statusBadge($o->payment_status) !!}</td>
                                            <td>{!! Order::statusBadge($o->order_status) !!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Top products + payment mix + low stock ── --}}
        <div class="row clearfix">
            <div class="col-lg-6">
                <div class="dash-card">
                    <div class="dc-head">
                        <h3>Top 5 selling products</h3>
                        <ul class="nav top-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#top-all" role="tab">All time</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#top-period" role="tab">{{ $periodLabel }}</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        @foreach (['top-all' => $topAllTime, 'top-period' => $topProducts] as $pane => $list)
                            <div class="tab-pane fade {{ $pane === 'top-all' ? 'show active' : '' }}" id="{{ $pane }}" role="tabpanel">
                                @if ($list->isEmpty())
                                    <div class="empty-note">No sales {{ $pane === 'top-all' ? 'yet' : 'in this period yet' }}.</div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table dash-table mb-0">
                                            <thead><tr><th>#</th><th>Product</th><th class="text-center">Units</th><th class="text-right">Revenue</th></tr></thead>
                                            <tbody>
                                                @foreach ($list as $i => $p)
                                                    <tr>
                                                        <td class="text-muted font-weight-bold">{{ $i + 1 }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="top-img" alt="" src="{{ $p->product_image ? asset('storage/products/thumb/' . $p->product_image) : asset('assets/images/products/product-1.jpg') }}">
                                                                <div>
                                                                    @if ($p->exists_id)
                                                                        <a href="{{ route('admin.edit_product', $p->exists_id) }}">{{ Str::limit($p->product_name, 46) }}</a>
                                                                    @else
                                                                        {{ Str::limit($p->product_name, 46) }} <span class="text-muted small">(removed)</span>
                                                                    @endif
                                                                    <div class="text-muted small">{{ $p->orders }} {{ Str::plural('order', $p->orders) }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center font-weight-bold">{{ $p->units }}</td>
                                                        <td class="text-right text-nowrap">₹{{ number_format((float) $p->revenue, 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="dash-card">
                    <div class="dc-head"><h3>Payment methods</h3><span class="text-muted small">{{ $periodLabel }}, excl. cancelled</span></div>
                    <div class="dc-body">
                        @php $mixTotal = max(1, $paymentMix->sum('orders')); @endphp
                        @forelse ($paymentMix as $m)
                            @php $pct = round($m->orders / $mixTotal * 100); $color = $m->payment_method === 'cod' ? '#6435c9' : '#2185d0'; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><strong>{{ Tx::gatewayLabel($m->payment_method) }}</strong> &middot; {{ $m->orders }} {{ Str::plural('order', $m->orders) }}</span>
                                    <span>₹{{ number_format((float) $m->value, 0) }} &middot; {{ $pct }}%</span>
                                </div>
                                <div class="mix-bar"><span style="width: {{ $pct }}%; background: {{ $color }}"></span></div>
                            </div>
                        @empty
                            <div class="empty-note">No orders in this period yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="dash-card" id="low-stock">
                    <div class="dc-head"><h3>Low stock</h3><a href="{{ route('admin.product') }}" class="small">Products</a></div>
                    @if ($lowStock->isEmpty())
                        <div class="empty-note"><i class="fa fa-check-circle text-success"></i> All items are above their stock alert level.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table dash-table mb-0">
                                <thead><tr><th>Item</th><th class="text-center">Stock</th><th class="text-center">Alert at</th></tr></thead>
                                <tbody>
                                    @foreach ($lowStock as $item)
                                        <tr>
                                            <td><a href="{{ route('admin.edit_product', $item->product_id) }}">{{ Str::limit($item->product_name, 40) }}</a>
                                                @if ($item->variant)<div class="text-muted small">{{ $item->variant }}</div>@endif</td>
                                            <td class="text-center">
                                                <span class="tag" style="background: {{ $item->stock <= 0 ? '#db2828' : '#e67e22' }}; color:#fff">{{ $item->stock <= 0 ? 'Out' : $item->stock }}</span>
                                            </td>
                                            <td class="text-center text-muted">{{ $item->alert_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('admin_assets/bundles/apexcharts.bundle.js') }}"></script>
<script>
    $(function () {
        $('#customToggle').on('click', function () { $('#customRange').toggleClass('d-none d-flex').find('[name=from]').trigger('focus'); });
        // keep "to" from going before "from"
        $('#customRange [name=from]').on('change', function () { $('#customRange [name=to]').attr('min', this.value); });

        if (typeof ApexCharts === 'undefined') return;
        const inr = v => '₹' + Math.round(v).toLocaleString('en-IN');

        @if (array_sum($sales['orders']) > 0)
        new ApexCharts(document.querySelector('#salesChart'), {
            chart: { height: 300, type: 'line', toolbar: { show: false }, fontFamily: 'inherit', zoom: { enabled: false } },
            series: [
                { name: 'Revenue', type: 'area', data: @json($sales['revenue']) },
                { name: 'Orders', type: 'column', data: @json($sales['orders']) },
            ],
            labels: @json($sales['labels']),
            colors: ['#21ba45', '#2185d0'],
            stroke: { width: [2, 0], curve: 'smooth' },
            fill: { opacity: [0.18, 0.85] },
            plotOptions: { bar: { columnWidth: '45%', borderRadius: 3 } },
            dataLabels: { enabled: false },
            legend: { position: 'top', horizontalAlign: 'right' },
            xaxis: { tickAmount: Math.min(10, @json(count($sales['labels']))), labels: { rotate: 0, hideOverlappingLabels: true } },
            yaxis: [
                { title: { text: 'Revenue' }, labels: { formatter: inr } },
                { opposite: true, title: { text: 'Orders' }, min: 0, forceNiceScale: true, labels: { formatter: v => Math.round(v) } },
            ],
            tooltip: { shared: true, intersect: false, y: { formatter: (v, { seriesIndex }) => seriesIndex === 0 ? inr(v) : v + ' orders' } },
            grid: { borderColor: '#f0f2f5' },
            responsive: [{ breakpoint: 576, options: {
                xaxis: { tickAmount: 4 },
                yaxis: [ { labels: { formatter: v => '₹' + (v >= 1000 ? Math.round(v / 1000) + 'k' : Math.round(v)) } },
                         { opposite: true, min: 0, labels: { formatter: v => Math.round(v) } } ],
            } }],
        }).render();
        @endif

        @if ($totalOrdersAll > 0)
        new ApexCharts(document.querySelector('#statusChart'), {
            chart: { type: 'donut', height: 190, fontFamily: 'inherit' },
            series: @json(array_values($statuses)),
            labels: @json(array_map('ucfirst', array_keys($statuses))),
            colors: @json(array_map(fn ($s) => Order::statusColor($s), array_keys($statuses))),
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Orders', formatter: () => '{{ $totalOrdersAll }}' } } } } },
            stroke: { width: 2 },
        }).render();
        @endif
    });
</script>
@endsection
