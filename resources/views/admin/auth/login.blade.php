@extends('admin.layouts.auth')

@section('title', 'Login')
@section('rightTitle', 'Welcome to SelfBuy Admin')
@section('rightText', 'Manage products, orders and customers — all in one place.')

@section('content')
    <div class="card">
        <div class="text-center mb-2">
            <a class="header-brand" href="{{ route('admin.login') }}"> <img src="{{ asset('selfbuy1.png') }}"
                    alt="logo" width="75" height="75" style="object-fit: contain;"></a>

        </div>
        <div class="card-body">
            <div class="card-title">Login to your account</div>
            @include('partials._message')
            <form action="{{ route('admin.login_process') }}" method="post" id="loginForm">
                @csrf
                <div class="form-group">
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ old('email', $rememberedEmail) }}" placeholder="Enter email">
                    @if ($errors->has('email'))
                        <div class="text-danger">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Password<a href="{{ route('admin.forget_password') }}" class="float-right small">I
                            forgot password</a></label>
                    <input type="password" class="form-control" name="password" id="password"
                        placeholder="Password" value="{{ old('password') }}">
                    @if ($errors->has('password'))
                        <div class="text-danger">{{ $errors->first('password') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember_me" value="1"
                            {{ old('remember_me', $rememberedChecked) ? 'checked' : '' }} id="remember_me" />
                        <span class="custom-control-label">Remember me</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-block" name="login"
                        id="login-submit">Sign in</button>
                </div>
            </form>
        </div>
        @if (config('auth.admin_registration'))
            <div class="text-center text-muted">
                Don't have account yet? <a href="{{ route('admin.register') }}">Register Here!</a>
            </div>
        @endif
    </div>
@endsection
