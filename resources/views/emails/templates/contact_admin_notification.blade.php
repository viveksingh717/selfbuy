@extends('emails.layouts.master')

@section('content')
    <h1>New contact message</h1>
    <p>A visitor has submitted the contact form on {{ config('app.name', 'SelfBuy') }}.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 8px 0 20px;">
        <tr>
            <td style="padding:8px 0; width:110px; color:#7A766C; vertical-align:top;">Name</td>
            <td style="padding:8px 0; font-weight:600;">{{ $contact->name }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0; color:#7A766C; vertical-align:top;">Email</td>
            <td style="padding:8px 0;"><a href="mailto:{{ $contact->email }}" style="color:#FF6B4A;">{{ $contact->email }}</a></td>
        </tr>
        @if ($contact->phone)
            <tr>
                <td style="padding:8px 0; color:#7A766C; vertical-align:top;">Phone</td>
                <td style="padding:8px 0;">{{ $contact->phone }}</td>
            </tr>
        @endif
        @if ($contact->subject)
            <tr>
                <td style="padding:8px 0; color:#7A766C; vertical-align:top;">Subject</td>
                <td style="padding:8px 0;">{{ $contact->subject }}</td>
            </tr>
        @endif
        <tr>
            <td style="padding:8px 0; color:#7A766C; vertical-align:top;">Received</td>
            <td style="padding:8px 0;">{{ $contact->created_at->format('d M Y, h:i A') }}</td>
        </tr>
    </table>

    <p class="text-muted" style="margin-bottom:6px;"><strong>Message</strong></p>
    <div style="background-color:#FAF8F4; border-radius:8px; padding:18px 20px; margin:0 0 20px; white-space:pre-line;">{{ $contact->message }}</div>

    <a href="{{ $adminUrl }}" class="btn-primary">Open in Admin</a>

    <p class="text-muted">Reply directly to this email to respond to {{ $contact->name }}.</p>
@endsection
