@extends('emails.layouts.master')

@section('content')
    <h1>Thanks for reaching out, {{ $contact->name }}!</h1>

    <p>
        We've received your message and a member of our team will get back to you
        within 24 hours. Here's a copy for your records:
    </p>

    @if ($contact->subject)
        <p class="text-muted" style="margin-bottom:4px;"><strong>Subject:</strong> {{ $contact->subject }}</p>
    @endif

    <div style="background-color:#FAF8F4; border-radius:8px; padding:18px 20px; margin:12px 0 20px; white-space:pre-line;">{{ $contact->message }}</div>

    <hr class="divider">

    <p class="text-muted">
        Need to add something? Just reply to this email and it will reach the same team.
    </p>
    <p class="text-muted">— The {{ config('app.name', 'SelfBuy') }} Team</p>
@endsection
