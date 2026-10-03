{{--
    Shared shell for every error page (404, 500, 503 maintenance, database down...).
    Must stay self-contained: no database, no settings, no Vite, no site layout -
    these pages are shown exactly when those things may be broken, and the 503 page
    is pre-rendered by `php artisan down --render` before the app is even booted.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    @hasSection('refresh')
        <meta http-equiv="refresh" content="@yield('refresh')">
    @endif
    <title>@yield('title') | SelfBuy</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root { --brand: #f5851f; --ink: #2d333a; --muted: #6b7280; --bg: #f7f7f9; --card: #ffffff; --line: #ececf1; }
        @media (prefers-color-scheme: dark) {
            :root { --ink: #f3f4f6; --muted: #a1a1aa; --bg: #17181b; --card: #212226; --line: #2e3036; }
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            display: flex; align-items: center; justify-content: center; padding: 24px 16px;
            background: var(--bg); color: var(--ink);
            font: 16px/1.6 system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .card {
            width: 100%; max-width: 520px; padding: 40px 32px; text-align: center;
            background: var(--card); border: 1px solid var(--line); border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }
        .logo { height: 40px; margin-bottom: 28px; }
        .code { font-size: 64px; font-weight: 700; line-height: 1; color: var(--brand); letter-spacing: -2px; }
        h1 { font-size: 22px; margin: 16px 0 8px; }
        p { margin: 0 auto; color: var(--muted); max-width: 400px; }
        .actions { margin-top: 28px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block; padding: 10px 22px; border-radius: 8px; font-weight: 600; font-size: 15px;
            text-decoration: none; border: 1px solid var(--brand); cursor: pointer; font-family: inherit;
        }
        .btn-primary { background: var(--brand); color: #fff; }
        .btn-outline { background: transparent; color: var(--brand); }
        .btn:hover { opacity: .9; }
        .note { margin-top: 24px; font-size: 13px; color: var(--muted); }
        .spinner {
            display: inline-block; width: 12px; height: 12px; margin-right: 6px; vertical-align: -1px;
            border: 2px solid var(--line); border-top-color: var(--brand); border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) { .spinner { animation: none; } }
    </style>
</head>
<body>
    <main class="card" role="main">
        <a href="{{ url('/') }}">
            {{-- White-text logo on dark backgrounds, the normal one otherwise. --}}
            <picture>
                <source srcset="{{ asset('selfbuy-logo-dark.svg') }}" media="(prefers-color-scheme: dark)">
                <img class="logo" src="{{ asset('selfbuy-logo.svg') }}" alt="SelfBuy">
            </picture>
        </a>
        <div class="code">@yield('code')</div>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        <div class="actions">
            @section('actions')
                <a class="btn btn-primary" href="{{ url('/') }}">Go to Home</a>
                <a class="btn btn-outline" href="javascript:history.back()">Go Back</a>
            @show
        </div>
        @hasSection('note')
            <div class="note">@yield('note')</div>
        @endif
    </main>
</body>
</html>
