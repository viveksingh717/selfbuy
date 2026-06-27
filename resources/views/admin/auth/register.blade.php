<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <link rel="icon" href="favicon.ico" type="image/x-icon" /> --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="SelfBuy">

    <title>Admin|Register</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/bootstrap/css/bootstrap.min.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Core css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/theme1.css') }}" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="SelfBuy">

</head>

<body class="font-montserrat">

    <div class="auth">
        <div class="auth_left">
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
                            <label class="form-label">Password<a href="javascript:void(0)" class="float-right small">I
                                    forgot password</a></label>
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
                    Already have account? <a href="{{ route('admin.login') }}">Sign in</a>
                </div>
            </div>
        </div>
        <div class="auth_right full_img" id="login_right_div"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin_assets/bundles/lib.vendor.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/core.js') }}"></script>

</body>

</html>
