@extends('emails.layouts.master')

@section('content')
    <h1>Your verification code</h1>
    <p>Use the code below to complete your sign-in. This code works whether you received it by email or SMS.</p>

    <div style="text-align:center; margin: 28px 0;">
        <span style="display:inline-block; padding: 16px 32px; background-color:#FAF8F4; border-radius:8px; font-size:32px; font-weight:700; letter-spacing:8px; color:#1B1A17;">
            {{ $otp }}
        </span>
    </div>

    <p class="text-muted">This code expires in 10 minutes. If you didn't request this, you can safely ignore this email.</p>
@endsection
