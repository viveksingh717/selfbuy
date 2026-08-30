@extends('emails.layouts.master')

@section('content')
    <h1>Your password was changed</h1>
    <p>Hi {{ $name }},</p>
    <p>This is a confirmation that the password for your {{ config('app.name') }} account was changed on {{ $changedAt }}.</p>

    <p class="text-muted">If you made this change, no action is needed.</p>

    <hr class="divider">

    <p>
        <strong>If you didn't make this change,</strong> your account may be compromised &mdash; please reset your
        password right away from the sign-in menu on <a href="{{ url('/') }}" style="color: #FF6B4A;">{{ config('app.name') }}</a>,
        or <a href="{{ url('/contact') }}" style="color: #FF6B4A;">contact our support team</a> for help.
    </p>
@endsection
