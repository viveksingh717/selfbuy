@extends('emails.layouts.master')

@php
    // ──────────────────────────────────────────────
    // FAKE / DEMO DATA — remove this block once you
    // wire up real $order data from your controller
    // ──────────────────────────────────────────────
    $order = (object) [
        'order_number' => 'EC-10482',
        'customer_name' => 'Aanya Mehta',
        'customer_email' => 'aanya.mehta@example.com',
        'created_at' => now()->subMinutes(12),
        'payment_method' => 'UPI — Google Pay',
        'estimated_delivery' => '24 June 2026',
        'subtotal' => 2400.0,
        'discount' => 200.0,
        'coupon_code' => 'SUMMER20',
        'shipping' => 0,
        'tax' => 108.0,
        'total' => 2308.0,
        'items' => collect([
            (object) [
                'product_name' => 'Classic Cotton Crewneck',
                'variant' => 'Navy / Large',
                'qty' => 2,
                'price' => 899.0,
                'product_image' => null,
            ],
            (object) [
                'product_name' => 'Trail Runner Sneakers',
                'variant' => 'UK 9',
                'qty' => 1,
                'price' => 602.0,
                'product_image' => null,
            ],
        ]),
        'shipping_address' => (object) [
            'name' => 'Aanya Mehta',
            'line1' => '12, Sea View Apartments, MG Road',
            'line2' => 'Near Phoenix Mall',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'phone' => '+91 98765 43210',
        ],
    ];

    $trackingUrl = '#';
    $userEmail = $order->customer_email; // used by emails.layouts.master footer
@endphp

@section('content')

    <h1>Order Confirmed 🎉</h1>

    <p>Hi {{ $order->customer_name }},</p>

    <p>
        Thanks for shopping with us! Your order
        <strong>#{{ $order->order_number }}</strong> has been confirmed and
        is now being prepared for shipment.
    </p>

    <a href="{{ $trackingUrl ?? '#' }}" class="btn-primary">Track Your Order</a>

    <hr class="divider">

    {{-- ── Order Meta Info ── --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
        <tr>
            <td style="padding: 4px 0; font-size: 13px; color: #7A766C; width: 50%;">
                Order Number
            </td>
            <td style="padding: 4px 0; font-size: 13px; color: #1B1A17; font-weight: 600; text-align: right;">
                #{{ $order->order_number }}
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 13px; color: #7A766C;">
                Order Date
            </td>
            <td style="padding: 4px 0; font-size: 13px; color: #1B1A17; font-weight: 600; text-align: right;">
                {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 13px; color: #7A766C;">
                Payment Method
            </td>
            <td style="padding: 4px 0; font-size: 13px; color: #1B1A17; font-weight: 600; text-align: right;">
                {{ $order->payment_method ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 13px; color: #7A766C;">
                Estimated Delivery
            </td>
            <td style="padding: 4px 0; font-size: 13px; color: #1B1A17; font-weight: 600; text-align: right;">
                {{ $order->estimated_delivery ?? 'Within 5-7 business days' }}
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ── Order Items Table ── --}}
    <p style="font-size: 15px; font-weight: 700; margin: 0 0 14px; color: #1B1A17;">
        Order Summary
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="border-collapse: collapse; margin-bottom: 8px;">

        {{-- table head --}}
        <tr>
            <td
                style="padding: 0 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #A8A398; border-bottom: 2px solid #EBE7DD;">
                Item
            </td>
            <td align="center"
                style="padding: 0 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #A8A398; border-bottom: 2px solid #EBE7DD;">
                Qty
            </td>
            <td align="right"
                style="padding: 0 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #A8A398; border-bottom: 2px solid #EBE7DD;">
                Price
            </td>
            <td align="right"
                style="padding: 0 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #A8A398; border-bottom: 2px solid #EBE7DD;">
                Total
            </td>
        </tr>

        {{-- table rows --}}
        @foreach ($order->items as $item)
            <tr>
                <td style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; vertical-align: middle;">
                    <table role="presentation" cellpadding="0" cellspacing="0">
                        <tr>
                            @if (!empty($item->product_image))
                                <td style="padding-right: 12px; vertical-align: middle;">
                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" width="48"
                                        height="48"
                                        style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #EBE7DD; display: block;">
                                </td>
                            @endif
                            <td style="vertical-align: middle;">
                                <span style="font-size: 14px; font-weight: 600; color: #1B1A17; display: block;">
                                    {{ $item->product_name }}
                                </span>
                                @if (!empty($item->variant))
                                    <span style="font-size: 12px; color: #A8A398;">
                                        {{ $item->variant }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td align="center"
                    style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; font-size: 13px; color: #1B1A17;">
                    {{ $item->qty }}
                </td>
                <td align="right"
                    style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; font-size: 13px; color: #1B1A17;">
                    ₹{{ number_format($item->price, 2) }}
                </td>
                <td align="right"
                    style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; font-size: 13px; font-weight: 600; color: #1B1A17;">
                    ₹{{ number_format($item->price * $item->qty, 2) }}
                </td>
            </tr>
        @endforeach

    </table>

    {{-- ── Price Breakdown ── --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top: 8px;">
        <tr>
            <td style="padding: 6px 0; font-size: 13px; color: #7A766C;">Subtotal</td>
            <td align="right" style="padding: 6px 0; font-size: 13px; color: #1B1A17;">
                ₹{{ number_format($order->subtotal, 2) }}
            </td>
        </tr>

        @if (($order->discount ?? 0) > 0)
            <tr>
                <td style="padding: 6px 0; font-size: 13px; color: #7A766C;">
                    Discount @if (!empty($order->coupon_code))
                        ({{ $order->coupon_code }})
                    @endif
                </td>
                <td align="right" style="padding: 6px 0; font-size: 13px; color: #5E8068;">
                    − ₹{{ number_format($order->discount, 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td style="padding: 6px 0; font-size: 13px; color: #7A766C;">Shipping</td>
            <td align="right" style="padding: 6px 0; font-size: 13px; color: #1B1A17;">
                {{ ($order->shipping ?? 0) > 0 ? '₹' . number_format($order->shipping, 2) : 'Free' }}
            </td>
        </tr>

        @if (($order->tax ?? 0) > 0)
            <tr>
                <td style="padding: 6px 0; font-size: 13px; color: #7A766C;">Tax</td>
                <td align="right" style="padding: 6px 0; font-size: 13px; color: #1B1A17;">
                    ₹{{ number_format($order->tax, 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td colspan="2" style="padding-top: 10px;">
                <hr style="border: none; border-top: 1px solid #EBE7DD; margin: 0;">
            </td>
        </tr>

        <tr>
            <td style="padding: 12px 0 0; font-size: 15px; font-weight: 700; color: #1B1A17;">
                Total
            </td>
            <td align="right" style="padding: 12px 0 0; font-size: 17px; font-weight: 700; color: #FF6B4A;">
                ₹{{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ── Shipping Address ── --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 12px;">
                <p
                    style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #A8A398; margin: 0 0 8px;">
                    Shipping Address
                </p>
                <p style="font-size: 13px; color: #1B1A17; margin: 0; line-height: 1.6;">
                    {{ $order->shipping_address->name ?? $order->customer_name }}<br>
                    {{ $order->shipping_address->line1 ?? '' }}<br>
                    @if (!empty($order->shipping_address->line2))
                        {{ $order->shipping_address->line2 }}<br>
                    @endif
                    {{ $order->shipping_address->city ?? '' }}, {{ $order->shipping_address->state ?? '' }}
                    {{ $order->shipping_address->pincode ?? '' }}<br>
                    {{ $order->shipping_address->phone ?? '' }}
                </p>
            </td>
            <td width="50%" style="vertical-align: top; padding-left: 12px;">
                <p
                    style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #A8A398; margin: 0 0 8px;">
                    Need Help?
                </p>
                <p style="font-size: 13px; color: #1B1A17; margin: 0; line-height: 1.6;">
                    Questions about your order? <br>
                    <a href="{{ url('/contact') }}" style="color: #FF6B4A;">Contact our support team</a>
                    or reply to this email.
                </p>
            </td>
        </tr>
    </table>

@endsection
