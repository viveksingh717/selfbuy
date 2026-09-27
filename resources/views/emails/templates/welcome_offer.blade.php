@extends('emails.layouts.master')

@php
    $money   = fn ($amount) => '₹' . rtrim(rtrim(number_format((float) $amount, 2), '0'), '.');
    $percent = \App\Services\OfferCouponService::percentText($coupon->discount_value);
@endphp

@section('content')
    <h1>Welcome to {{ config('app.name', 'SelfBuy') }}, {{ $user->name }}!</h1>

    <p>
        Your account is ready. As a thank-you for joining us, here's <strong>{{ $percent }}% off</strong> your first order.
    </p>

    <div style="background-color:#FAF8F4; border-radius:8px; padding:20px; margin:12px 0 20px; text-align:center;">
        <p style="margin:0 0 6px; font-size:16px;">Your welcome code:</p>
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

    <p style="text-align:center;">
        <a href="{{ $shopUrl }}" class="btn-primary">Start Shopping</a>
    </p>

    <hr class="divider">

    <p class="text-muted">You're receiving this because a {{ config('app.name', 'SelfBuy') }} account was created with {{ $user->email }}.</p>
    <p class="text-muted">— The {{ config('app.name', 'SelfBuy') }} Team</p>
@endsection
