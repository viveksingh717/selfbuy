<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="Selfbuy - eCommerce website developed by Vivek Singh">
    <meta name="author" content="Vivek Singh">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/selfbuy_apple_touch.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/icons/vivek_logo.png') }}">
    {{-- <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="SelfBuy"> --}}
    <link rel="manifest" href="{{ asset('assets/images/icons/site.html') }}">
    <link rel="mask-icon" href="{{ asset('assets/images/icons/safari_logo.svg') }}" color="#666666">
    <link rel="shortcut icon" href="{{ asset('assets/images/icons/favicon.ico') }}">
    <meta name="apple-mobile-web-app-title" content="SelfBuy">
    <meta name="application-name" content="SelfBuy">
    <meta name="msapplication-TileColor" content="#cc9966">
    <meta name="msapplication-config" content="{{ asset('assets/images/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">
    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/owl-carousel/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/magnific-popup/magnific-popup.css') }}">
    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
    <div class="page-wrapper">
        <!-- Header start -->
        @include('layouts.inc.header')
        <!-- End Header -->

        <main class="main">
            @yield('content')
        </main><!-- End .main -->

        <!-- Footer start -->
        @include('layouts.inc.footer')
        {{-- Footer end --}}

    </div><!-- End .page-wrapper -->
    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->
    @include('layouts.inc.mobile_menu')
    <!-- End .mobile-menu-container -->

    <!-- Sign in / Register Modal start-->
    @include('layouts.inc.navbar')
    <!-- Sign in / Register Modal end -->

    <!-- Plugins JS File -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.hoverIntent.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/superfish.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @yield('script')

</body>
<!-- SelfBuy/Vivek 26 Jun 2026 -->

</html>
