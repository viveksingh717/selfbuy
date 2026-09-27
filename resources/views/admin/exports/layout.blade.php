<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @page { margin: 26px 28px 40px; }
        * { font-family: 'DejaVu Sans', sans-serif; }            /* ships with dompdf, has the ₹ glyph */
        body { font-size: 9px; color: #333; margin: 0; }
        .hdr { width: 100%; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
        .hdr td { vertical-align: top; }
        .store { font-size: 16px; font-weight: bold; color: #1b1a17; }
        .muted { color: #777; }
        h1 { font-size: 15px; margin: 0 0 2px; }
        .summary { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px 10px; }
        .summary td { background: #f4f5f8; padding: 7px 9px; border-radius: 4px; }
        .summary .v { font-size: 12px; font-weight: bold; color: #1b1a17; }
        .summary .l { font-size: 8px; text-transform: uppercase; color: #777; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #2f3542; color: #fff; text-align: left; padding: 5px 6px; font-size: 8px; text-transform: uppercase; }
        table.data td { padding: 5px 6px; border-bottom: 1px solid #e6e8ec; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #fafbfc; }
        thead { display: table-header-group; }                  /* repeat the header on every page */
        tr { page-break-inside: avoid; }
        .r { text-align: right; } .c { text-align: center; }
        .pill { display: inline-block; padding: 1px 5px; border-radius: 3px; color: #fff; font-size: 8px; }
        .note { margin-top: 8px; color: #b35c00; }
        h2 { font-size: 11px; margin: 14px 0 6px; }
        .footer { position: fixed; bottom: -26px; left: 0; right: 0; font-size: 8px; color: #999; }
        .pagenum:before { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">
        {{ setting('site_name', config('app.name')) }} &middot; @yield('title') &middot; generated {{ $generatedAt->format('d M Y, h:i A') }}
        <span style="float:right">Page <span class="pagenum"></span></span>
    </div>

    <table class="hdr">
        <tr>
            <td>
                <div class="store">{{ setting('site_name', config('app.name', 'SelfBuy')) }}</div>
                <div class="muted">{{ setting('contact_email') }}{{ setting('contact_phone') ? ' · ' . setting('contact_phone') : '' }}</div>
            </td>
            <td style="text-align:right">
                <h1>@yield('title')</h1>
                <div class="muted">@yield('subtitle')</div>
                <div class="muted">Generated {{ $generatedAt->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    @yield('content')
</body>
</html>
