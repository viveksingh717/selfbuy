@extends('emails.layouts.master')

@section('content')
    <h1>Your account details were updated</h1>
    <p>Hi {{ $name }},</p>
    <p>This is a confirmation that your {{ config('app.name') }} account details were changed on {{ $changedAt }}.</p>

    <table style="width:100%; margin: 20px 0; border-collapse: collapse;">
        @foreach ($changes as $change)
            <tr>
                <td style="padding:6px 12px 6px 0; color:#999; white-space:nowrap;"><strong>{{ $change['label'] }}</strong></td>
                <td style="padding:6px 0; color:#666;">{{ $change['old'] ?: '(not set)' }} &rarr; <strong style="color:#1B1A17;">{{ $change['new'] }}</strong></td>
            </tr>
        @endforeach
    </table>

    <p class="text-muted">If you made this change, no action is needed.</p>

    <hr class="divider">

    <p>
        <strong>If you didn't make this change,</strong> your account may be compromised &mdash; please reset your
        password right away from the sign-in menu on <a href="{{ url('/') }}" style="color: #FF6B4A;">{{ config('app.name') }}</a>,
        or <a href="{{ url('/contact') }}" style="color: #FF6B4A;">contact our support team</a> for help.
    </p>
@endsection
