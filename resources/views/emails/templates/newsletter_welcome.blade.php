@extends('emails.layouts.master')

@php
    $money = fn ($amount) => '₹' . rtrim(rtrim(number_format((float) $amount, 2), '0'), '.');
    if ($coupon) {
        $percent = rtrim(rtrim(number_format((float) $coupon->discount_value, 2, '.', ''), '0'), '.');
    }
@endphp

@section('content')
    <h1>You're subscribed!</h1>

    <p>
        Thanks for joining the {{ config('app.name', 'SelfBuy') }} newsletter. You'll be the first to hear about
        new arrivals, trending products and exclusive offers.
    </p>

    @if ($coupon)
        <div style="background-color:#FAF8F4; border-radius:8px; padding:20px; margin:12px 0 20px; text-align:center;">
            <p style="margin:0 0 6px; font-size:16px;">Here's your <strong>{{ $percent }}% off</strong> welcome code:</p>
            <p style="margin:0 0 10px; font-size:26px; font-weight:700; letter-spacing:3px; font-family:Consolas, Menlo, monospace;
                border:2px dashed #FF6B4A; border-radius:8px; padding:10px 14px; display:inline-block; background:#FFFFFF;">{{ $coupon->coupon_code }}</p>
            <p class="text-muted" style="margin:0; font-size:13px;">
                Enter it in your cart at checkout.
                @if ((float) $coupon->min_order_amount > 0)
                    Valid on orders of {{ $money($coupon->min_order_amount) }} or more.
                @endif
                @if ($coupon->max_discount_amount)
                    Maximum discount {{ $money($coupon->max_discount_amount) }}.
                @endif
                One use only.
                @if ($coupon->expiry_date)
                    Valid until {{ \App\Services\OfferCouponService::lastValidDay($coupon)->format('j M Y') }}.
                @endif
            </p>
        </div>
    @endif

    <p style="text-align:center;">
        <a href="{{ $shopUrl }}" class="btn-primary">Start Shopping</a>
    </p>

    <hr class="divider">

    <p class="text-muted">
        You're receiving this because {{ $subscriber->email }} was signed up on our website.
        Not you, or changed your mind? <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>.
    </p>
    <p class="text-muted">— The {{ config('app.name', 'SelfBuy') }} Team</p>
@endsection
