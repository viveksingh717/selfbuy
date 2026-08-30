@extends('admin.layouts.auth')

@section('title', 'Reset Password')
@section('rightTitle', 'Almost There')
@section('rightText', 'Set a new password to get back into your SelfBuy admin account.')

@section('content')
    <div class="card">
        <div class="text-center mb-2">
            <a class="header-brand" href="{{ route('admin.login') }}"> <img src="{{ asset('selfbuy1.png') }}"
                    alt="logo" width="75" height="75" style="object-fit: contain;"></a>

        </div>
        <div class="card-body">
            <div class="card-title">Reset password</div>
            <p class="text-muted">Choose a new password for your admin account.</p>
            @include('partials._message')
            <form action="{{ route('admin.reset_password') }}" method="post" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ old('email', $email) }}" placeholder="Enter email" required>
                    @if ($errors->has('email'))
                        <div class="text-danger">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New password</label>
                    <input type="password" class="form-control" name="password" id="password"
                        placeholder="New password" minlength="6" required>
                    @if ($errors->has('password'))
                        <div class="text-danger">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                    <input type="password" class="form-control" name="password_confirmation"
                        id="password_confirmation" placeholder="Confirm new password" minlength="6" required>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-block">Reset password</button>
                </div>
            </form>
        </div>
        <div class="text-center text-muted">
            Remembered it? <a href="{{ route('admin.login') }}">Back to Login</a>
        </div>
    </div>
@endsection
