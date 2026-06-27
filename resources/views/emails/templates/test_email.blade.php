@extends('emails.layouts.master')

@section('content')
    <h1>Verify your email address</h1>

    <p>Hello Vivek Singh,</p>

    <p>Thanks for signing up! Please click the button below to verify your email address and activate your account.</p>

    {{-- <a href="{{ $verificationUrl }}" class="btn-primary">Verify Email Address</a> --}}
    <a href="http://selfbuy.com/admin/" class="btn-primary">Verify Email Address</a>

    <p class="text-muted">This link will expire in 60 minutes.</p>

    <hr class="divider">

    <p class="text-muted">If the button doesn't work, copy and paste this URL into your browser:</p>
    <p class="fallback-link">http://selfbuy.com/admin</p>

    <hr class="divider">

    <p class="text-muted">If you did not create an account, no further action is required.</p>
@endsection
