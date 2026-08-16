@extends('emails.layouts.master')

@php
    $customerName = trim($order->first_name.' '.$order->last_name);
    $paymentMethodLabel = $order->paymentMethodLabel();
    $trackingUrl = route('checkout.success', $order->order_number);
    $userEmail = $order->email; // used by emails.layouts.master footer
@endphp

@section('content')

    <h1>Order Confirmed 🎉</h1>

    <p>Hi {{ $customerName }},</p>

    <p>
        Thanks for shopping with us! Your order
        <strong>#{{ $order->order_number }}</strong> has been confirmed and
        is now being prepared for shipment.
    </p>

    <a href="{{ $trackingUrl }}" class="btn-primary">View Your Order</a>

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
                {{ $order->created_at->format('d M Y, h:i A') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 13px; color: #7A766C;">
                Payment Method
            </td>
            <td style="padding: 4px 0; font-size: 13px; color: #1B1A17; font-weight: 600; text-align: right;">
                {{ $paymentMethodLabel }}
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 13px; color: #7A766C;">
                Estimated Delivery
            </td>
            <td style="padding: 4px 0; font-size: 13px; color: #1B1A17; font-weight: 600; text-align: right;">
                Within 5-7 business days
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
                    <span style="font-size: 14px; font-weight: 600; color: #1B1A17; display: block;">
                        {{ $item->product_name }}
                    </span>
                    @if (!empty($item->variant_label))
                        <span style="font-size: 12px; color: #A8A398;">
                            {{ $item->variant_label }}
                        </span>
                    @endif
                </td>
                <td align="center"
                    style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; font-size: 13px; color: #1B1A17;">
                    {{ $item->qty }}
                </td>
                <td align="right"
                    style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; font-size: 13px; color: #1B1A17;">
                    ₹{{ number_format($item->unit_price + $item->extra_price, 2) }}
                </td>
                <td align="right"
                    style="padding: 14px 0; border-bottom: 1px solid #EBE7DD; font-size: 13px; font-weight: 600; color: #1B1A17;">
                    ₹{{ number_format($item->line_total, 2) }}
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

        @if ($order->discount > 0)
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
                {{ $order->shipping_cost > 0 ? '₹'.number_format($order->shipping_cost, 2) : 'Free' }}
            </td>
        </tr>

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
                    {{ $customerName }}<br>
                    {{ $order->address_line1 }}<br>
                    @if (!empty($order->address_line2))
                        {{ $order->address_line2 }}<br>
                    @endif
                    {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                    {{ $order->phone }}
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
