<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="SelfBuy">

    <title>@yield('title', 'SelfBuy Admin')</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/bootstrap/css/bootstrap.min.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Core css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/theme1.css') }}" />

    @yield('style')
</head>

<body class="font-montserrat">

    <div class="auth">
        <div class="auth_left" style="background-image: url('{{ asset('admin_assets/images/leftpanel.svg') }}'); background-size: cover; background-position: 25% center;">
            @yield('content')
        </div>
        <div class="auth_right full_img" id="login_right_div" style="background-image: url('{{ asset('admin_assets/images/rightpanel.svg') }}'); background-size: cover; background-position: 75% center; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding-top: 8%; box-sizing: border-box;">
            <h2 style="color: #1D4ED8; font-weight: 700; font-size: 2rem; margin-bottom: 10px;">@yield('rightTitle', 'Welcome to SelfBuy Admin')</h2>
            <p style="color: #3B5B94; font-size: 1.1rem; max-width: 380px; text-align: center; padding: 0 20px;">@yield('rightText', 'Manage products, orders and customers — all in one place.')</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin_assets/bundles/lib.vendor.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/core.js') }}"></script>

    @yield('script')
</body>

</html>
