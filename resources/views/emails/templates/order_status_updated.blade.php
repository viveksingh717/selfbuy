@extends('emails.layouts.master')

@php
    $status = $order->order_status;
    $headline = [
        'pending'    => 'We have received your order',
        'processing' => 'Your order is being prepared',
        'shipped'    => 'Your order is on its way!',
        'delivered'  => 'Your order has been delivered',
        'cancelled'  => 'Your order has been cancelled',
    ][$status] ?? 'Your order has been updated';
    $blurb = [
        'pending'    => "We've got your order and will start preparing it shortly.",
        'processing' => "We're packing your items and will let you know as soon as they ship.",
        'shipped'    => 'Your package has left our warehouse.',
        'delivered'  => 'We hope you love your purchase. Thank you for shopping with us!',
        'cancelled'  => 'Your order has been cancelled. If you already paid online, any refund will be processed to your original payment method.',
    ][$status] ?? '';
@endphp

@section('content')
    <h1>{{ $headline }}</h1>

    <p>Hi {{ $order->first_name }}, {{ $blurb }}</p>

    <div style="background-color:#FAF8F4; border-radius:8px; padding:16px 20px; margin:12px 0 20px;">
        <p style="margin:0 0 6px;"><strong>Order:</strong> #{{ $order->order_number }}</p>
        <p style="margin:0 0 6px;"><strong>Status:</strong> {{ ucfirst($status) }}</p>
        <p style="margin:0;"><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>
        @if ($status === 'cancelled' && $order->cancel_reason)
            <p style="margin:6px 0 0;"><strong>Reason:</strong> {{ $order->cancel_reason }}</p>
        @endif
    </div>

    @if ($order->tracking_number && $status !== 'cancelled')
        <div style="border:1px solid #EBE7DD; border-radius:8px; padding:16px 20px; margin:0 0 20px;">
            <p style="margin:0 0 6px; font-weight:600;">Shipment tracking</p>
            @if ($order->tracking_courier)
                <p style="margin:0 0 4px;">Courier: {{ $order->tracking_courier }}</p>
            @endif
            <p style="margin:0 0 {{ $order->tracking_url ? '10px' : '0' }};">Tracking number: <strong>{{ $order->tracking_number }}</strong></p>
            @if ($order->tracking_url)
                <a href="{{ $order->tracking_url }}" class="btn-primary" style="margin:0;">Track your package</a>
            @endif
        </div>
    @endif

    <p style="text-align:center;">
        <a href="{{ $orderUrl }}" class="btn-primary">View your order</a>
    </p>

    <hr class="divider">
    <p class="text-muted">Questions about your order? Just reply to this email.</p>
    <p class="text-muted">— The {{ config('app.name', 'SelfBuy') }} Team</p>
@endsection
