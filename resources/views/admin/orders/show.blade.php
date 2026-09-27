@extends('admin.layouts.app')

@section('title', 'Order #' . $order->order_number)
@section('subTitle', 'Order Details')

@php
    $badge = fn ($s) => \App\Models\Order::statusBadge($s);
    $money = fn ($v) => '₹' . number_format((float) $v, 2);
    $next = $order->nextStatuses();
    $historyIcon = ['order' => 'fa-flag', 'payment' => 'fa-credit-card', 'tracking' => 'fa-truck', 'email' => 'fa-envelope', 'note' => 'fa-sticky-note'];
@endphp

@section('style')
    <style>
        .order-meta dt { font-weight: 500; color: #9aa0ac; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; }
        .order-meta dd { margin-bottom: 10px; }
        .order-item-img { width: 44px; height: 44px; object-fit: cover; border-radius: 4px; border: 1px solid #e8e9e9; }
        .order-timeline { list-style: none; margin: 0; padding: 0; }
        .order-timeline li { position: relative; padding: 0 0 16px 32px; }
        .order-timeline li:before { content: ''; position: absolute; left: 11px; top: 24px; bottom: 0; width: 2px; background: #e8e9e9; }
        .order-timeline li:last-child:before { display: none; }
        .order-timeline .tl-icon { position: absolute; left: 0; top: 0; width: 24px; height: 24px; border-radius: 50%; background: #f0f2f5; color: #6e7687; display: flex; align-items: center; justify-content: center; font-size: 11px; }
        .order-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        @media (max-width: 575.98px) {
            .order-items td:first-child, .order-items th:first-child { min-width: 180px; } /* table scrolls instead of squeezing */
            .order-items .order-item-img { display: none; }
        }
    </style>
@endsection

@section('content')
<div class="section-body mt-3">
    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('admin.orders') }}" class="text-muted"><i class="fa fa-arrow-left"></i> Back to orders</a>
        </div>

        @include('partials._message')

        <div class="row clearfix">
            {{-- ══════════ Main column ══════════ --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex justify-content-between align-items-start">
                            <div class="mb-3">
                                <h4 class="mb-1">Order #{{ $order->order_number }}</h4>
                                <div class="text-muted small mb-2">Placed {{ $order->created_at->format('d M Y, h:i A') }} ({{ $order->created_at->diffForHumans() }})</div>
                                {!! $badge($order->order_status) !!}
                                <span class="ml-1">Payment: {!! $badge($order->payment_status) !!}</span>
                            </div>
                            <div class="order-actions">
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-file-pdf-o"></i> Invoice</a>
                                <button type="button" class="btn btn-sm btn-outline-secondary js-order-email" data-type="confirmation" data-busy="Sending..."><i class="fa fa-envelope-o"></i> Resend confirmation</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary js-order-email" data-type="status" data-busy="Sending..."><i class="fa fa-paper-plane-o"></i> Send status update</button>
                                @if ($order->order_status === 'cancelled')
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="deleteOrder" data-busy="Deleting..."><i class="fa fa-trash"></i> Delete</button>
                                @endif
                            </div>
                        </div>
                        @if ($order->order_status === 'cancelled')
                            <div class="alert alert-danger mb-0 mt-2">
                                Cancelled {{ optional($order->cancelled_at)->format('d M Y, h:i A') }}@if ($order->cancel_reason) - {{ $order->cancel_reason }}@endif.
                                {{ $order->stock_restored ? 'Stock has been returned to inventory.' : '' }}
                                @if ($order->payment_status === 'paid')
                                    <strong>This order was paid - refund the customer, then mark the payment as Refunded.</strong>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tracking journey --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Order journey</h3></div>
                    <div class="card-body">
                        @include('partials.order_journey', ['order' => $order])
                    </div>
                </div>

                {{-- Items + totals --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Items ({{ $order->items->sum('qty') }})</h3></div>
                    <div class="table-responsive">
                        <table class="table table-vcenter mb-0 order-items">
                            <thead>
                                <tr><th>Product</th><th class="text-right">Price</th><th class="text-center">Qty</th><th class="text-right">Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    @php $img = optional($item->product)->product_image; @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img class="order-item-img mr-2" alt=""
                                                    src="{{ $img ? asset('storage/products/thumb/' . $img) : asset('assets/images/products/product-1.jpg') }}">
                                                <div>
                                                    @if ($item->product)
                                                        <a href="{{ route('product.details', $item->product->product_slug) }}" target="_blank">{{ $item->product_name }}</a>
                                                    @else
                                                        {{ $item->product_name }} <span class="text-muted small">(product removed)</span>
                                                    @endif
                                                    @if ($item->variant_label)
                                                        <div class="text-muted small">{{ $item->variant_label }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">{{ $money($item->unit_price + $item->extra_price) }}</td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td class="text-right">{{ $money($item->line_total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3" class="text-right">Subtotal</td><td class="text-right">{{ $money($order->subtotal) }}</td></tr>
                                @if ((float) $order->discount > 0)
                                    <tr><td colspan="3" class="text-right">Discount @if ($order->coupon_code)<span class="tag tag-default">{{ $order->coupon_code }}</span>@endif</td><td class="text-right text-success">-{{ $money($order->discount) }}</td></tr>
                                @endif
                                <tr><td colspan="3" class="text-right">Shipping ({{ ucfirst($order->shipping_method) }})</td><td class="text-right">{{ (float) $order->shipping_cost > 0 ? $money($order->shipping_cost) : 'Free' }}</td></tr>
                                <tr><td colspan="3" class="text-right font-weight-bold">Total</td><td class="text-right font-weight-bold">{{ $money($order->total) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Activity timeline --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Activity</h3></div>
                    <div class="card-body">
                        <form class="js-order-form mb-4" action="{{ route('admin.orders.note', $order->id) }}">
                            <div class="input-group">
                                <input type="text" name="note" class="form-control" maxlength="1000" placeholder="Add an internal note (not shown to the customer)" required>
                                <div class="input-group-append"><button class="btn btn-primary" type="submit" data-busy="Adding...">Add note</button></div>
                            </div>
                        </form>

                        <ul class="order-timeline">
                            @foreach ($order->histories as $h)
                                <li>
                                    <span class="tl-icon"><i class="fa {{ $historyIcon[$h->type] ?? 'fa-circle' }}"></i></span>
                                    <div>
                                        @if ($h->type === 'order')
                                            Status changed {!! $h->from_value ? $badge($h->from_value) . ' &rarr; ' : '' !!}{!! $badge($h->to_value) !!}
                                        @elseif ($h->type === 'payment')
                                            Payment {!! $h->from_value ? $badge($h->from_value) . ' &rarr; ' : '' !!}{!! $badge($h->to_value) !!}
                                        @elseif ($h->type === 'tracking')
                                            Tracking updated
                                        @elseif ($h->type === 'email')
                                            Email sent to customer
                                        @else
                                            Note
                                        @endif
                                        @if ($h->customer_notified && $h->type !== 'email')
                                            <span class="tag tag-default ml-1"><i class="fa fa-envelope"></i> emailed</span>
                                        @endif
                                    </div>
                                    @if ($h->note)
                                        <div class="text-muted" style="white-space:pre-line">{{ $h->note }}</div>
                                    @endif
                                    <div class="text-muted small">{{ $h->created_at->format('d M Y, h:i A') }} &middot; {{ optional($h->admin)->name ?? 'System' }}</div>
                                </li>
                            @endforeach
                            <li>
                                <span class="tl-icon"><i class="fa fa-shopping-cart"></i></span>
                                <div>Order placed by the customer ({{ $order->paymentMethodLabel() }})</div>
                                <div class="text-muted small">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ══════════ Side column ══════════ --}}
            <div class="col-lg-4">
                {{-- Customer --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Customer</h3></div>
                    <div class="card-body">
                        <dl class="order-meta mb-0">
                            <dt>Name</dt>
                            <dd>{{ $order->customerName() }}
                                <span class="tag tag-{{ $order->user_id ? 'info' : 'default' }} ml-1">{{ $order->user_id ? 'Registered' : 'Guest' }}</span></dd>
                            <dt>Email</dt><dd><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></dd>
                            <dt>Phone</dt><dd><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></dd>
                            <dt>Shipping address</dt>
                            <dd>
                                {{ $order->address_line1 }}@if ($order->address_line2), {{ $order->address_line2 }}@endif<br>
                                {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                                {{ $order->country }}
                            </dd>
                            @if ($order->order_notes)
                                <dt>Customer note</dt><dd style="white-space:pre-line">{{ $order->order_notes }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Order status --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Order status</h3></div>
                    <div class="card-body">
                        @if (empty($next))
                            <p class="mb-0 text-muted">{!! $badge($order->order_status) !!} is a final status - no further changes.</p>
                        @else
                            <form class="js-order-form" action="{{ route('admin.orders.status', $order->id) }}" id="statusForm">
                                <div class="form-group">
                                    <label>Change to</label>
                                    <select name="order_status" class="form-control" required>
                                        @foreach ($next as $s)
                                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Note <span class="text-muted small">(cancellation reason / internal note)</span></label>
                                    <textarea name="note" class="form-control" rows="2" maxlength="500"></textarea>
                                </div>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="notify" value="1" checked>
                                    <span class="custom-control-label">Email the customer about this update</span>
                                </label>
                                <button type="submit" class="btn btn-primary btn-block mt-3" data-busy="Updating status...">Update status</button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Payment --}}
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Payment</h3></div>
                    <div class="card-body">
                        <dl class="order-meta">
                            <dt>Method</dt><dd>{{ strtoupper($order->payment_method) }} <span class="text-muted">({{ $order->paymentMethodLabel() }})</span></dd>
                            <dt>Status</dt><dd>{!! $badge($order->payment_status) !!}</dd>
                        </dl>
                        @foreach ($order->payments as $p)
                            <div class="border rounded p-2 mb-2 small">
                                <div class="d-flex justify-content-between"><strong>{{ ucfirst($p->gateway) }}</strong>{!! $badge($p->status) !!}</div>
                                <div>{{ $p->currencySymbol() }}{{ number_format((float) $p->amount, 2) }} {{ $p->currency }}</div>
                                @if ($p->gateway_payment_id)<div class="text-muted text-break">Payment ID: {{ $p->gateway_payment_id }}</div>@endif
                                @if ($p->gateway_order_id)<div class="text-muted text-break">Gateway order: {{ $p->gateway_order_id }}</div>@endif
                                @if ($p->paid_at)<div class="text-muted">Paid {{ $p->paid_at->format('d M Y, h:i A') }}</div>@endif
                                @if ($p->failure_reason)<div class="text-danger">{{ $p->failure_reason }}</div>@endif
                            </div>
                        @endforeach
                        <form class="js-order-form mt-3" action="{{ route('admin.orders.payment_status', $order->id) }}">
                            <div class="form-group">
                                <label>Mark payment as</label>
                                <select name="payment_status" class="form-control">
                                    @foreach (\App\Models\Order::PAYMENT_STATUSES as $s)
                                        <option value="{{ $s }}" @selected($s === $order->payment_status)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="note" class="form-control" maxlength="500" placeholder="Note (e.g. refund reference)">
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-block" data-busy="Updating payment...">Update payment</button>
                        </form>
                    </div>
                </div>

                {{-- Tracking --}}
                @if ($order->order_status !== 'cancelled')
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Shipment tracking</h3></div>
                        <div class="card-body">
                            <form class="js-order-form" action="{{ route('admin.orders.tracking', $order->id) }}">
                                <div class="form-group">
                                    <label>Courier</label>
                                    <input type="text" name="tracking_courier" class="form-control" maxlength="100" value="{{ $order->tracking_courier }}" placeholder="e.g. Delhivery, Blue Dart">
                                </div>
                                <div class="form-group">
                                    <label>Tracking number</label>
                                    <input type="text" name="tracking_number" class="form-control" maxlength="100" value="{{ $order->tracking_number }}">
                                </div>
                                <div class="form-group">
                                    <label>Tracking link</label>
                                    <input type="url" name="tracking_url" class="form-control" maxlength="500" value="{{ $order->tracking_url }}" placeholder="https://...">
                                </div>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="notify" value="1">
                                    <span class="custom-control-label">Email the tracking details to the customer</span>
                                </label>
                                <button type="submit" class="btn btn-outline-primary btn-block mt-3" data-busy="Saving tracking...">Save tracking</button>
                            </form>
                            @if ($order->shipped_at)
                                <div class="text-muted small mt-2">Shipped {{ $order->shipped_at->format('d M Y, h:i A') }}@if ($order->delivered_at) &middot; Delivered {{ $order->delivered_at->format('d M Y, h:i A') }}@endif</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        const csrf = $('meta[name="csrf-token"]').attr('content');

        // Button feedback while a request runs: spinner + "Updating..." label, locked against double clicks.
        function setBusy($btn, busy) {
            if (!$btn || !$btn.length) return;
            if (busy) {
                $btn.data('idle-html', $btn.html());
                $btn.html('<i class="fa fa-spinner fa-spin"></i> ' + ($btn.data('busy') || 'Please wait...'))
                    .prop('disabled', true).attr('aria-busy', 'true');
            } else {
                $btn.html($btn.data('idle-html')).prop('disabled', false).removeAttr('aria-busy');
            }
        }

        function send(url, method, data, $btn) {
            setBusy($btn, true);
            // Lock the page's other action buttons too, so two updates can't overlap.
            const $others = $('.js-order-form [type=submit], .js-order-email, #deleteOrder').not($btn).prop('disabled', true);
            return $.ajax({ url, method, data, headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } })
                .done(res => {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 900);   // stays "Updating..." until the page refreshes
                })
                .fail(xhr => {
                    const r = xhr.responseJSON || {};
                    const firstError = r.errors ? Object.values(r.errors)[0][0] : null;
                    toastr.error(firstError || r.message || 'Something went wrong.');
                    setBusy($btn, false);
                    $others.prop('disabled', false);
                });
        }

        $('.js-order-form').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this), $btn = $form.find('[type=submit]');
            const go = () => send($form.attr('action'), 'POST', $form.serialize(), $btn);

            // Cancelling returns stock and can't be undone - confirm first.
            if ($form.is('#statusForm') && $form.find('[name=order_status]').val() === 'cancelled') {
                Swal.fire({
                    icon: 'warning', title: 'Cancel this order?',
                    text: 'The items go back into stock and this cannot be undone.',
                    showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, cancel order',
                }).then(r => r.isConfirmed && go());
                return;
            }
            go();
        });

        $('.js-order-email').on('click', function () {
            const $btn = $(this);
            const what = $btn.data('type') === 'confirmation' ? 'the order confirmation' : 'a status update';
            Swal.fire({
                icon: 'question', title: 'Email the customer?', text: `Send ${what} to {{ $order->email }}.`,
                showCancelButton: true, confirmButtonText: 'Send',
            }).then(r => r.isConfirmed && send('{{ route('admin.orders.send_email', $order->id) }}', 'POST', { type: $btn.data('type') }, $btn));
        });

        $('#deleteOrder').on('click', function () {
            Swal.fire({
                icon: 'warning', title: 'Delete order #{{ $order->order_number }}?',
                text: 'The order and its history are removed permanently. Payment records are kept.',
                showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete',
            }).then(r => {
                if (!r.isConfirmed) return;
                const $btn = $('#deleteOrder');
                setBusy($btn, true);
                $.ajax({ url: '{{ route('admin.orders.delete', $order->id) }}', method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } })
                    .done(res => { toastr.success(res.message); setTimeout(() => location.href = '{{ route('admin.orders') }}', 800); })
                    .fail(xhr => { toastr.error((xhr.responseJSON || {}).message || 'Could not delete the order.'); setBusy($btn, false); });
            });
        });
    });
</script>
@endsection
