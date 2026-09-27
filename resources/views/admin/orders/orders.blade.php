@extends('admin.layouts.app')

@section('title', 'All Orders')
@section('subTitle', 'Order List')

@section('style')
    <style>
        .order-stat { cursor: pointer; border: 1px solid #e8e9e9; border-radius: 6px; padding: 12px 14px; background: #fff; transition: border-color .15s; }
        .order-stat:hover, .order-stat.active { border-color: #467fcf; }
        .order-stat .num { font-size: 22px; font-weight: 600; line-height: 1.2; }
        .order-stat .lbl { font-size: 12px; color: #9aa0ac; text-transform: uppercase; letter-spacing: .03em; }
        #ordersTable td { vertical-align: middle; }
    </style>
@endsection

@section('content')
<div class="section-body mt-3">
    <div class="container-fluid">
        @include('partials._message')

        {{-- Status summary - click a tile to filter the list --}}
        <div class="row clearfix mb-3">
            @php
                $tiles = ['' => ['All Orders', '#343a40']] + collect(\App\Models\Order::STATUSES)
                    ->mapWithKeys(fn ($s) => [$s => [ucfirst($s), \App\Models\Order::statusColor($s)]])->all();
            @endphp
            @foreach ($tiles as $status => [$label, $color])
                <div class="col-6 col-md-4 col-lg-2 mb-2">
                    <div class="order-stat {{ $status === '' ? 'active' : '' }}" data-status="{{ $status }}">
                        <div class="num" style="color: {{ $color }}">{{ $status === '' ? $total : ($counts[$status] ?? 0) }}</div>
                        <div class="lbl">{{ $label }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="orderFilters" class="row" autocomplete="off">
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-2">
                                <input type="text" class="form-control" name="q" placeholder="Order no., customer, email or phone">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <select class="form-control" name="order_status">
                                    <option value="">All statuses</option>
                                    @foreach (\App\Models\Order::STATUSES as $s)
                                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <select class="form-control" name="payment_status">
                                    <option value="">All payments</option>
                                    @foreach (\App\Models\Order::PAYMENT_STATUSES as $s)
                                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-4 col-sm-6 mb-2">
                                <select class="form-control" name="payment_method">
                                    <option value="">Method</option>
                                    @foreach ($methods as $m)
                                        <option value="{{ $m }}">{{ strtoupper($m) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <input type="date" class="form-control" name="date_from" title="From date">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <input type="date" class="form-control" name="date_to" title="To date">
                            </div>
                            <div class="col-12 d-flex" style="gap:8px">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                <button type="reset" class="btn btn-outline-secondary" id="resetFilters">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-vcenter mb-0 text-nowrap" id="ordersTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        const $filters = $('#orderFilters');
        const filterValues = () => Object.fromEntries(new FormData($filters[0]));

        const table = $('#ordersTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,            // the filter bar above replaces the default search box
            ajax: {
                url: '{{ route('admin.orders') }}',
                data: d => Object.assign(d, filterValues()),
            },
            columns: [
                { data: 'order_number', name: 'order_number' },
                { data: 'customer',     name: 'customer' },
                { data: 'items_count',  name: 'items_count', searchable: false },
                { data: 'total',        name: 'total', searchable: false },
                { data: 'payment',      name: 'payment', searchable: false },
                { data: 'order_status', name: 'order_status' },
                { data: 'created_at',   name: 'created_at' },
                { data: 'action',       name: 'action', orderable: false, searchable: false },
            ],
            order: [[6, 'desc']],
            pageLength: 25,
            language: { emptyTable: 'No orders match these filters.' },
        });

        $filters.on('submit', function (e) {
            e.preventDefault();
            const status = $filters.find('[name=order_status]').val();
            $('.order-stat').removeClass('active').filter(`[data-status="${status}"]`).addClass('active');
            table.ajax.reload();
        });

        $('#resetFilters').on('click', function () {
            setTimeout(() => $filters.trigger('submit'), 0); // after the form has cleared itself
        });

        // Status tiles act as quick filters
        $('.order-stat').on('click', function () {
            $filters.find('[name=order_status]').val($(this).data('status'));
            $filters.trigger('submit');
        });
    });
</script>
@endsection
