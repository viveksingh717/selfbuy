@extends('admin.layouts.auth')

@section('title', 'Forgot Password')
@section('rightTitle', 'Forgot Your Password?')
@section('rightText', "No problem — we'll help you get back into your SelfBuy admin account.")

@section('content')
    <div class="card">
        <div class="text-center mb-2">
            <a class="header-brand" href="{{ route('admin.login') }}"> <img src="{{ asset('selfbuy1.png') }}"
                    alt="logo" width="75" height="75" style="object-fit: contain;"></a>

        </div>
        <div class="card-body">
            <div class="card-title">Forgot password</div>
            <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
            @include('partials._message')
            <form action="{{ route('admin.password.email') }}" method="post" id="forgetForm">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ old('email') }}" placeholder="Enter email" required>
                    @if ($errors->has('email'))
                        <div class="text-danger">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-block">Send reset link</button>
                </div>
            </form>
        </div>
        <div class="text-center text-muted">
            Forget it, <a href="{{ route('admin.login') }}">Send me Back</a> to the Login page.
        </div>
    </div>
@endsection
