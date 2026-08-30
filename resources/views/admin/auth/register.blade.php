@extends('admin.layouts.auth')

@section('title', 'Admin|Register')
@section('rightTitle', 'Join SelfBuy Admin')
@section('rightText', 'Create your account to start managing the store.')

@section('content')
    <div class="card">
        <div class="text-center mb-2">
            <a class="header-brand" href="{{ route('admin.register') }}"><img src="{{ asset('selfbuy1.png') }}"
                    alt="logo" width="75" height="75" style="object-fit: contain;"></a>

        </div>
        <div class="card-body">
            <div class="card-title">Create new account</div>
            @include('partials._message')
            <form action="{{ route('admin.register_process') }}" method="post" id="registerForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" id="name"
                        value="{{ old('name') }}" placeholder="Enter name">
                    @if ($errors->has('name'))
                        <div class="text-danger">{{ $errors->first('name') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ old('email') }}" placeholder="Enter email">
                    @if ($errors->has('email'))
                        <div class="text-danger">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password"
                        placeholder="Password" value="{{ old('password') }}">
                    @if ($errors->has('password'))
                        <div class="text-danger">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="terms" value="1"
                            {{ old('terms') ? 'checked' : '' }} id="terms" />
                        <span class="custom-control-label">Agree the <a
                                href="{{ route('admin.terms_condition') }}" target="_blank">terms and
                                policy</a></span>
                    </label>
                    @if ($errors->has('terms'))
                        <div class="text-danger">{{ $errors->first('terms') }}</div>
                    @endif
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-block" name="register"
                        id="register-submit">Create new account</button>
                </div>
            </form>
        </div>
        <div class="text-center text-muted">
            Already have account? <a href="{{ route('admin.login') }}">Login Here!</a>
        </div>
    </div>
@endsection
