<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F4F4F7;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        table {
            border-collapse: collapse;
        }

        .email-wrapper {
            width: 100%;
            background-color: #F4F4F7;
            padding: 40px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* ── Header ── */
        .email-header {
            background-color: #000000;
            padding: 4px 6px;
            text-align: center;
        }

        /* .email-header img {
            height: 80px;
        } */

        .email-header .brand-name {
            color: #FFFFFF;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
        }

        /* ── Body ── */
        .email-body {
            padding: 40px;
            color: #1B1A17;
            font-size: 15px;
            line-height: 1.65;
        }

        .email-body h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 16px;
            color: #1B1A17;
        }

        .email-body p {
            margin: 0 0 16px;
        }

        .btn-primary {
            display: inline-block;
            background-color: #FF6B4A;
            color: #FFFFFF !important;
            text-decoration: none;
            padding: 13px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin: 8px 0 20px;
        }

        .divider {
            border: none;
            border-top: 1px solid #EBE7DD;
            margin: 28px 0;
        }

        .text-muted {
            color: #7A766C;
            font-size: 13px;
        }

        .fallback-link {
            word-break: break-all;
            color: #FF6B4A;
            font-size: 13px;
        }

        /* ── Footer ── */
        .email-footer {
            padding: 28px 40px;
            text-align: center;
            background-color: #FAF8F4;
        }

        .email-footer p {
            margin: 0 0 6px;
            font-size: 12.5px;
            color: #A8A398;
        }

        .email-footer a {
            color: #7A766C;
            text-decoration: underline;
        }

        .social-links {
            margin: 14px 0 4px;
        }

        .social-links a {
            display: inline-block;
            margin: 0 6px;
        }

        /* .email-header img {
            height: 70px;
            max-height: 85px;
            width: auto;
            max-width: 220px;
        } */

        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                border-radius: 0;
            }

            .email-header,
            .email-body,
            .email-footer {
                padding: 24px 20px !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <div class="email-container">

                        {{-- ── HEADER (same for every email) ── --}}
                        <div class="email-header">
                            <img src="{{ asset('selfbuy.png') }}" alt="{{ config('app.name', 'SelfBuy') }}"
                                style="height: 100px; width: auto; display: block; margin: 0 auto;">
                        </div>

                        {{-- ── DYNAMIC CONTENT (changes per email) ── --}}
                        <div class="email-body">
                            {{ $slot ?? '' }}
                            @yield('content')
                        </div>

                        {{-- ── FOOTER (same for every email) ── --}}
                        <div class="email-footer">
                            <p>&copy; {{ date('Y') }} {{ config('app.name', 'SelfBuy') }}. All rights reserved.</p>
                            <p>
                                <a href="{{ url('/privacy-policy') }}">Privacy Policy</a> &nbsp;|&nbsp;
                                <a href="{{ url('/terms') }}">Terms of Service</a> &nbsp;|&nbsp;
                                <a href="{{ url('/contact') }}">Contact Us</a>
                            </p>
                            <p class="text-muted">
                                This email was sent to {{ $userEmail ?? 'you' }} because you have an account with us.
                            </p>
                        </div>

                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
