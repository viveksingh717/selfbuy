<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="favicon.ico" type="image/x-icon" />

    <title>Admin|Login</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/bootstrap/css/bootstrap.min.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Core css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/theme1.css') }}" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="EchoCart">

</head>

<body class="font-montserrat">

    <div class="auth">
        <div class="auth_left">
            <div class="card">
                <div class="text-center mb-2">
                    <a class="header-brand" href="{{ route('admin.login') }}"> <img src="{{ asset('shopping.png') }}"
                            alt="logo" width="35" height="35" style="object-fit: contain;"> Login</a>

                </div>
                <div class="card-body">
                    <div class="card-title">Login to your account</div>
                    @include('partials._message')
                    <form action="{{ route('admin.login_process') }}" method="post" id="loginForm">
                        @csrf
                        <div class="form-group">
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
                                <input type="checkbox" class="custom-control-input" name="remember_me" value="1"
                                    {{ old('remember_me') ? 'checked' : '' }} id="remember_me" />
                                <span class="custom-control-label">Remember me</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary btn-block" name="login"
                                id="login-submit">Sign in</button>
                        </div>
                    </form>
                </div>
                <div class="text-center text-muted">
                    Don't have account yet? <a href="javascript:void(0)">Sign up</a>
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
