@extends('emails.layouts.master')

@section('content')
    <h1>Reset your password</h1>
    <p>Hi {{ $name }},</p>
    <p>We received a request to reset the password for your {{ config('app.name') }} account. Click the button below to choose a new one.</p>

    <div style="text-align:center; margin: 28px 0;">
        <a href="{{ $url }}" class="btn-primary">Reset Password</a>
    </div>

    <p class="text-muted">This link expires in 60 minutes. If you didn't request a password reset, you can safely ignore this email &mdash; your password won't be changed.</p>

    <hr class="divider">
    <p class="text-muted">Or copy and paste this URL into your browser:</p>
    <p class="fallback-link">{{ $url }}</p>
@endsection
