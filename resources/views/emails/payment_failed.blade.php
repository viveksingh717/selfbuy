@extends('emails.layouts.master')

@php
    $customerName = trim(($billing['first_name'] ?? '').' '.($billing['last_name'] ?? '')) ?: 'there';
    $userEmail = $billing['email'] ?? 'you'; // used by emails.layouts.master footer
@endphp

@section('content')

    <h1>We couldn't complete your payment</h1>

    <p>Hi {{ $customerName }},</p>

    <p>
        Your payment of <strong>₹{{ number_format($payment->amount, 2) }}</strong> for your recent
        {{ config('app.name') }} order didn't go through, so the order wasn't placed. No money has been deducted
        for this attempt &mdash; if an amount was held by your bank, it will be released automatically within a
        few business days.
    </p>

    @if (!empty($payment->failure_reason))
        <p class="text-muted">Reason: {{ $payment->failure_reason }}</p>
    @endif

    <a href="{{ route('checkout.index') }}" class="btn-primary">Try Again</a>

    <hr class="divider">

    <p class="text-muted">
        Your cart items are still saved, nothing was lost. If you keep running into trouble, reply to this email
        or <a href="{{ url('/contact') }}" style="color: #FF6B4A;">contact our support team</a> and we'll help you
        complete your order.
    </p>

@endsection
