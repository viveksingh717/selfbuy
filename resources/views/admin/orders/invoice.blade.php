<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        /* dompdf: DejaVu Sans ships with it and has the ₹ glyph */
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 11px; color: #333; margin: 0; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #1b1a17; }
        .muted { color: #777; }
        .row { width: 100%; }
        .row td { vertical-align: top; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.items th { background: #f4f5f8; text-align: left; padding: 7px 8px; font-size: 10px; text-transform: uppercase; }
        table.items td { padding: 7px 8px; border-bottom: 1px solid #eee; }
        .r { text-align: right; }
        .c { text-align: center; }
        table.totals { width: 45%; margin-left: 55%; border-collapse: collapse; margin-top: 10px; }
        table.totals td { padding: 5px 8px; }
        table.totals .grand td { border-top: 2px solid #333; font-weight: bold; font-size: 13px; }
        .box { border: 1px solid #eee; padding: 10px 12px; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    @php $money = fn ($v) => '₹' . number_format((float) $v, 2); @endphp

    <table class="row">
        <tr>
            <td style="width:55%">
                <h1>{{ setting('site_name', config('app.name', 'SelfBuy')) }}</h1>
                <div class="muted">
                    {{ setting('address_line') }}<br>
                    {{ trim(setting('address_city', '') . ', ' . setting('address_state', '') . ' ' . setting('address_postcode', ''), ', ') }}<br>
                    {{ setting('contact_email') }} &middot; {{ setting('contact_phone') }}
                </div>
            </td>
            <td class="r">
                <h1>INVOICE</h1>
                <div><strong>#{{ $order->order_number }}</strong></div>
                <div class="muted">Date: {{ $order->created_at->format('d M Y') }}</div>
                <div class="muted">Payment: {{ $order->paymentMethodLabel() }} ({{ ucfirst($order->payment_status) }})</div>
            </td>
        </tr>
    </table>

    <table class="row" style="margin-top:18px">
        <tr>
            <td style="width:50%; padding-right:8px">
                <div class="box">
                    <strong>Bill / Ship to</strong><br>
                    {{ $order->customerName() }}<br>
                    {{ $order->address_line1 }}@if ($order->address_line2), {{ $order->address_line2 }}@endif<br>
                    {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                    {{ $order->country }}<br>
                    {{ $order->email }} &middot; {{ $order->phone }}
                </div>
            </td>
            <td style="width:50%; padding-left:8px">
                <div class="box">
                    <strong>Order status:</strong> {{ ucfirst($order->order_status) }}<br>
                    <strong>Shipping:</strong> {{ ucfirst($order->shipping_method) }}<br>
                    @if ($order->tracking_number)
                        <strong>Tracking:</strong> {{ $order->tracking_courier }} {{ $order->tracking_number }}<br>
                    @endif
                    @if ($order->coupon_code)
                        <strong>Coupon:</strong> {{ $order->coupon_code }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr><th>#</th><th>Item</th><th class="r">Price</th><th class="c">Qty</th><th class="r">Amount</th></tr>
        </thead>
        <tbody>
            @foreach ($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product_name }}@if ($item->variant_label)<br><span class="muted">{{ $item->variant_label }}</span>@endif</td>
                    <td class="r">{{ $money($item->unit_price + $item->extra_price) }}</td>
                    <td class="c">{{ $item->qty }}</td>
                    <td class="r">{{ $money($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="r">{{ $money($order->subtotal) }}</td></tr>
        @if ((float) $order->discount > 0)
            <tr><td>Discount</td><td class="r">-{{ $money($order->discount) }}</td></tr>
        @endif
        <tr><td>Shipping</td><td class="r">{{ (float) $order->shipping_cost > 0 ? $money($order->shipping_cost) : 'Free' }}</td></tr>
        <tr class="grand"><td>Total</td><td class="r">{{ $money($order->total) }}</td></tr>
    </table>

    <div class="footer">Thank you for shopping with {{ setting('site_name', config('app.name', 'SelfBuy')) }}. This is a computer-generated invoice.</div>
</body>
</html>
