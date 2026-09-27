@extends('admin.layouts.app')

@section('title', 'Transaction History')
@section('subTitle', 'Transaction History')

@use('App\Services\TransactionHistoryService', 'Tx')

@php
    $by = $summary['byStatus'];
    $collectedAll = collect([$summary['collected']['online'], $summary['collected']['cod']])
        ->reduce(function ($carry, $map) { foreach ($map as $c => $v) { $carry[$c] = ($carry[$c] ?? 0) + $v; } return $carry; }, []);
    $cards = [
        ['Collected', Tx::moneyList($collectedAll), $by['paid']['count'] . ' successful', '#21ba45',
            'Online ' . Tx::moneyList($summary['collected']['online']) . ' · COD ' . Tx::moneyList($summary['collected']['cod'])],
        ['Awaiting payment', Tx::moneyList($by['pending']['amounts']), $by['pending']['count'] . ' pending', '#f2a007', 'Unpaid COD orders + unfinished online payments'],
        ['Failed', Tx::moneyList($by['failed']['amounts']), $by['failed']['count'] . ' attempts', '#db2828', 'Declined / abandoned online payments'],
        ['Refunded', Tx::moneyList($by['refunded']['amounts']), $by['refunded']['count'] . ' refunds', '#6c757d', 'Marked refunded on the order page'],
    ];
@endphp

@section('style')
    <style>
        .tx-card { border-left: 4px solid; border-radius: 6px; background: #fff; padding: 14px 16px; height: 100%; }
        .tx-card .lbl { font-size: 12px; text-transform: uppercase; letter-spacing: .03em; color: #9aa0ac; }
        .tx-card .amt { font-size: 20px; font-weight: 600; line-height: 1.3; word-break: break-word; }
        .tx-card .sub { font-size: 12px; color: #6e7687; }
        #txTable td { vertical-align: middle; }
        #txTable td:nth-child(1), #txTable td:nth-child(6) { white-space: nowrap; }   /* date, amount */
        #txTable .text-break { word-break: break-all; }                               /* long gateway IDs */
    </style>
@endsection

@section('content')
<div class="section-body mt-3">
    <div class="container-fluid">
        <div class="row clearfix mb-2">
            @foreach ($cards as [$label, $amount, $count, $color, $hint])
                <div class="col-12 col-sm-6 col-xl-3 mb-3">
                    <div class="tx-card" style="border-left-color: {{ $color }}">
                        <div class="lbl">{{ $label }}</div>
                        <div class="amt" style="color: {{ $color }}">{{ $amount }}</div>
                        <div class="sub"><strong>{{ $count }}</strong> &middot; {{ $hint }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body">
                <form id="txFilters" class="row" autocomplete="off">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <input type="text" name="q" class="form-control" maxlength="100" placeholder="Transaction ID, order no., customer or email">
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <select name="gateway" class="form-control">
                            <option value="">All methods</option>
                            @foreach (Tx::GATEWAYS as $g)
                                <option value="{{ $g }}">{{ Tx::gatewayLabel($g) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <select name="status" class="form-control">
                            <option value="">All statuses</option>
                            @foreach (Tx::STATUSES as $s)
                                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2"><input type="date" name="date_from" class="form-control" title="From date"></div>
                    <div class="col-lg-2 col-md-4 mb-2"><input type="date" name="date_to" class="form-control" title="To date"></div>
                    <div class="col-12 d-flex flex-wrap" style="gap:8px">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <button type="reset" class="btn btn-outline-secondary" id="txReset">Reset</button>
                        <span class="ml-auto d-flex" style="gap:8px">
                            <a href="{{ route('admin.transactions.export', 'csv') }}" class="btn btn-outline-success js-export" data-format="csv"><i class="fa fa-file-excel-o"></i> Export CSV</a>
                            <a href="{{ route('admin.transactions.export', 'pdf') }}" class="btn btn-outline-danger js-export" data-format="pdf"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-vcenter mb-0" id="txTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transaction</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        const $filters = $('#txFilters');
        // Links like ?order_status=pending (e.g. from the dashboard) pre-fill the filters.
        new URLSearchParams(location.search).forEach((value, key) => $filters.find(`[name="${key}"]`).val(value));
        const filterValues = () => Object.fromEntries([...new FormData($filters[0])].filter(([, v]) => v !== ''));

        const table = $('#txTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,               // the filter bar replaces the default search box
            ajax: { url: '{{ route('admin.transactions') }}', data: d => Object.assign(d, filterValues()) },
            columns: [
                { data: 'created_at',   name: 'created_at' },
                { data: 'transaction',  name: 'transaction' },
                { data: 'order_number', name: 'order_number' },
                { data: 'customer',     name: 'customer' },
                { data: 'gateway',      name: 'gateway' },
                { data: 'amount',       name: 'amount' },
                { data: 'status',       name: 'status' },
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: { emptyTable: 'No transactions match these filters.' },
        });

        // Keep the export link in sync with the filters, so the CSV matches what's on screen.
        const exportBase = { csv: '{{ route('admin.transactions.export', 'csv') }}', pdf: '{{ route('admin.transactions.export', 'pdf') }}' };
        function syncExport() {
            const qs = new URLSearchParams(filterValues()).toString();
            $('.js-export').each(function () { $(this).attr('href', exportBase[$(this).data('format')] + (qs ? '?' + qs : '')); });
        }

        $filters.on('submit', function (e) { e.preventDefault(); syncExport(); table.ajax.reload(); });
        $('#txReset').on('click', () => setTimeout(() => $filters.trigger('submit'), 0));
        syncExport();
    });
</script>
@endsection
