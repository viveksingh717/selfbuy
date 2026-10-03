<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <style>
        /* ── Admin auth pages (login / register / forgot / reset) ──
           Desktop: form panel on the left, illustration + welcome text on the right.
           Below 992px: right panel hidden, form card centred full-width and scrollable. */
        .auth { min-height: 100vh; height: auto; align-items: stretch; }
        .auth .auth_left {
            background-image: url('{{ asset('admin_assets/images/leftpanel.svg') }}');
            background-size: cover; background-position: 25% center;
            width: 440px; min-height: 100vh; height: auto; padding: 32px 24px;
            display: flex; align-items: center; justify-content: center;
        }
        .auth .auth_left .card { width: 100%; max-width: 400px; margin: 0; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, .18); }
        .auth .auth_left .card-body { padding: 8px 28px 20px; }
        .auth .auth_left .auth-card-footer { padding: 0 28px 24px; line-height: 1.6; }
        .auth .auth_left .auth-card-footer a { white-space: nowrap; } /* "Register Here!" stays on one line */
        .auth .auth_left .card-title { font-size: 18px; font-weight: 600; margin-bottom: 18px; }
        .auth .auth_left .btn-block { min-height: 44px; font-weight: 600; }
        .auth .auth_right.full_img {
            background-image: url('{{ asset('admin_assets/images/rightpanel.svg') }}');
            background-size: cover; background-position: 75% center;
            flex-direction: column; align-items: center; justify-content: flex-start;
            padding-top: 8%; box-sizing: border-box; min-height: 100vh; height: auto;
        }
        .auth .auth_right h2 { color: #1D4ED8; font-weight: 700; font-size: 2rem; margin-bottom: 10px; }
        .auth .auth_right p { color: #3B5B94; font-size: 1.1rem; max-width: 380px; text-align: center; padding: 0 20px; }

        @media (min-width: 992px) {
            .auth .auth_right.full_img { display: flex; }
        }

        @media (max-width: 991.98px) {
            .auth { display: block; }
            .auth .auth_right { display: none !important; }
            .auth .auth_left { width: 100%; padding: 24px 16px; }
            /* The desktop illustration gets its icons cut off on narrow screens - use a clean
               gradient in the same navy instead. */
            .auth .auth_left {
                background-image: radial-gradient(120% 60% at 50% 0%, #10307a 0%, rgba(16, 48, 122, 0) 60%),
                                  linear-gradient(180deg, #071a4a 0%, #0a2463 100%);
                background-size: cover;
            }
            /* Phones (any orientation) + tablets: 16px inputs stop iOS Safari zooming in when a
               field is tapped; 44px is a comfortable touch target. */
            .auth .auth_left .form-control { font-size: 16px; height: 44px; }
            .auth .auth_left .card-title { text-align: center; }          /* in line with the centred logo */
            .auth .auth_left .form-footer { margin-top: 1.25rem; }        /* theme's 2rem left a big gap above the button */
        }

        @media (max-width: 575.98px) {
            /* centred vertically; a form taller than the screen just makes the page scroll */
            .auth .auth_left { padding-top: 24px; padding-bottom: 24px; }
            /* the theme adds 20px padding around the card - drop it so fields use the width */
            .auth .auth_left .card { padding: 16px 0 4px; }
            .auth .auth_left .card-body { padding: 4px 18px 12px; }
            .auth .auth_left .auth-card-footer { padding: 0 18px 16px; }
        }
    </style>

    @yield('style')
</head>

<body class="font-montserrat">

    <div class="auth">
        <div class="auth_left">
            @yield('content')
        </div>
        <div class="auth_right full_img" id="login_right_div">
            <h2>@yield('rightTitle', 'Welcome to SelfBuy Admin')</h2>
            <p>@yield('rightText', 'Manage products, orders and customers — all in one place.')</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin_assets/bundles/lib.vendor.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/core.js') }}"></script>
    <script src="{{ asset('admin_assets/js/form-loading.js') }}"></script>

    @yield('script')
</body>

</html>
