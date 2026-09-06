@extends('emails.layouts.master')

@section('content')
    <h1>Hi {{ $name }},</h1>

    <p>Thanks for getting in touch with {{ config('app.name', 'SelfBuy') }}. Here's our reply:</p>

    <div style="background-color:#FAF8F4; border-radius:8px; padding:18px 20px; margin:16px 0; white-space:pre-line;">{{ $replyBody }}</div>

    <hr class="divider">

    <p class="text-muted"><strong>Your original message:</strong></p>
    <p class="text-muted" style="white-space:pre-line;">{{ $original }}</p>

    <p class="text-muted">If you still need help, just reply to this email.</p>
@endsection
