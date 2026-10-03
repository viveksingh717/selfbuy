<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <link rel="icon" href="favicon.ico" type="image/x-icon"/> --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" title="SelfBuy">

    <title>@yield('title')</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/bootstrap/css/bootstrap.min.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{asset('admin_assets/css/font/fontawesome.css')}}" /> --}}
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/summernote/dist/summernote.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/sweetalert/sweetalert.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Plugins css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/charts-c3/c3.min.css') }}" />

    <!-- Core css -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/theme1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/css/admin-custom.css') }}?v={{ filemtime(public_path('admin_assets/css/admin-custom.css')) }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @vite('resources/js/module/admin.js')

    @yield('style')


</head>

<body class="font-montserrat">
    {{-- Re-apply saved theme settings before paint to avoid a flash --}}
    <script>
        (function () {
            try {
                var cfg = JSON.parse(localStorage.getItem('selfbuy_admin_settings')) || {};
                var body = document.body;
                var fonts = ['font-opensans', 'font-montserrat', 'font-roboto'];
                var font = fonts.indexOf(cfg.font) > -1 ? cfg.font : 'font-montserrat';
                fonts.forEach(function (f) { body.classList.remove(f); });
                body.classList.add(font);
                var map = {
                    'btn-darkmode': 'dark-mode',
                    'btn-sidebar': 'sidebar_dark',
                    'btn-iconcolor': 'iconcolor',
                    'btn-gradient': 'gradient',
                    'btn-rtl': 'rtl',
                    'btn-boxlayout': 'boxlayout'
                };
                Object.keys(map).forEach(function (k) {
                    if (cfg[k]) { body.classList.add(map[k]); }
                });
            } catch (e) {}
        })();
    </script>

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
        </div>
    </div>

    <div id="main_content">

        <div id="header_top" class="header_top">
            <div class="container">
                <div class="hleft">
                    <a class="header-brand" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('selfbuy-icon.svg') }}" alt="SelfBuy" class="brand-icon brand-icon-light">
                        <img src="{{ asset('selfbuy-icon-dark.svg') }}" alt="SelfBuy" class="brand-icon brand-icon-dark">
                    </a>
                </div>
                <div class="hright">
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="nav-link icon settingbar"><i class="fa fa-gear fa-spin"
                                data-toggle="tooltip" data-placement="right" title="Settings"></i></a>
                        <a href="javascript:void(0)" class="nav-link icon menu_toggle"><i
                                class="fa  fa-align-left"></i></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar start --}}
        @include('admin.layouts.inc.sidebar')

        <div class="page">
            {{-- Top-Navigation Header --}}
            @if (!Request::is('admin/chat'))
                @include('admin.layouts.inc.header')
            @endif
            {{-- @include('admin.layouts.inc.header') --}}

            {{-- Reminder on every admin page while the storefront is offline. --}}
            @if (app()->isDownForMaintenance() && !Request::is('admin/settings'))
                <div class="alert alert-warning mb-0 rounded-0 text-center py-2">
                    <i class="fa fa-wrench mr-1"></i> The store is in <strong>maintenance mode</strong> - visitors can't see it.
                    <a href="{{ route('admin.settings') }}" class="alert-link ml-1">Manage</a>
                </div>
            @endif

            {{-- Main Content --}}
            <div class="content-wrapper">
                @yield('content')
            </div>

            {{-- Footer Here --}}
            @if (!Request::is('admin/chat'))
                <div class="section-body footer-wrapper">
                    @include('admin.layouts.inc.footer')
                </div>
            @endif
            {{-- <div class="section-body">
        @include('admin.layouts.inc.footer')
    </div> --}}
            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="{{ asset('admin_assets/bundles/lib.vendor.bundle.js') }}"></script>
            <script src="{{ asset('admin_assets/js/core.js') }}"></script>
            <script src="{{ asset('admin_assets/js/admin-settings.js') }}"></script>
            <script src="{{ asset('admin_assets/js/form-loading.js') }}"></script>

            <!-- 3. Summernote -->
            <script src="{{ asset('admin_assets/bundles/summernote.bundle.js') }}"></script>
            <script src="{{ asset('admin_assets/bundles/sweetalert.bundle.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

            <!-- 4. DataTables -->
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

            <script type="text/javascript">
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            </script>

            @yield('script')
        </div>
    </div>

</body>

</html>
