@extends('layouts.insight')
@section('title', 'Complete Payment')

@php
    $snapshot = $payment->order_snapshot ?? [];
    $items = $snapshot['items'] ?? [];
    $totals = $snapshot['totals'] ?? [];
    $billing = $payment->billing_data ?? [];
@endphp

@section('content')
    <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
        <div class="container">
            <h1 class="page-title">Complete Payment</h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}">Checkout</a></li>
                <li class="breadcrumb-item active" aria-current="page">Payment</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="rz-status-card">
                        <div id="razorpay-status">
                            <div class="rz-spinner" aria-hidden="true"></div>
                            <h2 class="checkout-title mb-2">Opening secure payment&hellip;</h2>
                            <p class="text-muted">A Razorpay window should appear automatically. If it doesn't,
                                click below.</p>

                            <button type="button" id="razorpay-open-btn" class="btn btn-outline-primary-2 mt-2">
                                <span>Pay ₹{{ number_format($payment->amount, 2) }} Now</span>
                                <i class="icon-long-arrow-right"></i>
                            </button>

                            <p class="text-center mt-4 mb-0">
                                <a href="{{ route('checkout.index') }}">&larr; Cancel and return to checkout</a>
                            </p>
                        </div>
                    </div><!-- End .rz-status-card -->

                    <div class="rz-security-note">
                        <i class="icon-certificate"></i>
                        <span>Payments are processed securely by Razorpay. SelfBuy never sees or stores your card,
                            UPI, or bank details.</span>
                    </div>

                    @if (config('app.env') !== 'production')
                        <div class="rz-test-note">
                            <strong>Test mode &mdash; </strong>
                            this is a Razorpay sandbox payment, no real money moves. Cards, netbanking, and wallets
                            work normally with Razorpay's test credentials. <strong>UPI can't be tested by scanning
                            the QR code with a real banking app</strong> &mdash; real apps reject test transactions.
                            Instead, choose UPI in the widget and enter the ID <code>success@razorpay</code> (or
                            <code>failure@razorpay</code> to simulate a decline).
                        </div>
                    @endif
                </div><!-- End .col-lg-8 -->

                <aside class="col-lg-4">
                    <div class="summary">
                        <h3 class="summary-title">Order Summary</h3><!-- End .summary-title -->

                        <table class="table table-summary">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            {{ $item['product_name'] }}
                                            <strong> &times; {{ $item['qty'] }}</strong>
                                            @if (!empty($item['variant_label']))
                                                <br><small class="text-muted">{{ $item['variant_label'] }}</small>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($item['line_total'], 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr class="summary-subtotal">
                                    <td>Subtotal:</td>
                                    <td>₹{{ number_format($totals['subtotal'] ?? $payment->amount, 2) }}</td>
                                </tr>

                                @if (($totals['discount'] ?? 0) > 0)
                                    <tr class="summary-discount">
                                        <td>Discount @if (!empty($totals['coupon_code']))({{ $totals['coupon_code'] }})@endif:</td>
                                        <td>-₹{{ number_format($totals['discount'], 2) }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td>Shipping:</td>
                                    <td>
                                        @if (($totals['shipping'] ?? 0) > 0)
                                            ₹{{ number_format($totals['shipping'], 2) }}
                                        @else
                                            Free
                                        @endif
                                    </td>
                                </tr>

                                <tr class="summary-total">
                                    <td>Total:</td>
                                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table><!-- End .table table-summary -->

                        <div class="rz-billing-to">
                            <h4>Billing To</h4>
                            <p class="text-muted mb-0">
                                {{ trim(($billing['first_name'] ?? '').' '.($billing['last_name'] ?? '')) }}<br>
                                {{ $billing['address_line1'] ?? '' }}<br>
                                {{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['postal_code'] ?? '' }}<br>
                                {{ $billing['email'] ?? '' }}
                            </p>
                        </div>
                    </div><!-- End .summary -->
                </aside><!-- End .col-lg-4 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
@endsection

@section('style')
    <style>
        .rz-status-card {
            background: #fff;
            border: 1px solid #ebebeb;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
        }
        .rz-spinner {
            width: 40px;
            height: 40px;
            margin: 0 auto 20px;
            border: 3px solid #f0ebe3;
            border-top-color: #c96;
            border-radius: 50%;
            animation: rz-spin 0.8s linear infinite;
        }
        @keyframes rz-spin {
            to { transform: rotate(360deg); }
        }
        .rz-security-note {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            padding: 14px 16px;
            background: #f4f8f4;
            border: 1px solid #dcebdc;
            border-radius: 6px;
            font-size: 1.3rem;
            color: #4a6b4a;
        }
        .rz-security-note i {
            font-size: 1.8rem;
            flex-shrink: 0;
        }
        .rz-test-note {
            margin-top: 12px;
            padding: 14px 16px;
            background: #fff8ec;
            border: 1px solid #f0e0b8;
            border-radius: 6px;
            font-size: 1.3rem;
            color: #7a5f1f;
        }
        .rz-test-note code {
            background: #fff;
            border: 1px solid #f0e0b8;
            border-radius: 3px;
            padding: 1px 5px;
            color: #7a5f1f;
        }
        .rz-billing-to {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #ebebeb;
        }
        .rz-billing-to h4 {
            font-size: 1.4rem;
            margin-bottom: 6px;
        }
    </style>
@endsection

@section('script')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        $(function () {
            var billing = @json($billing);

            function showState(html) {
                $('#razorpay-status').html(html);
            }

            function openRazorpay() {
                var options = {
                    key: '{{ $razorpayKey }}',
                    amount: {{ (int) round($payment->amount * 100) }},
                    currency: '{{ $payment->currency }}',
                    name: '{{ config('app.name') }}',
                    description: 'Order payment',
                    order_id: '{{ $payment->gateway_order_id }}',
                    prefill: {
                        name: (billing.first_name || '') + ' ' + (billing.last_name || ''),
                        email: billing.email || '',
                        contact: billing.phone || ''
                    },
                    theme: { color: '#c96' },
                    handler: function (response) {
                        showState(
                            '<div class="rz-spinner"></div>' +
                            '<h2 class="checkout-title mb-2">Verifying payment&hellip;</h2>' +
                            '<p class="text-muted">Please don\'t close this page.</p>'
                        );

                        $.ajax({
                            url: '{{ route('payment.razorpay.verify') }}',
                            method: 'POST',
                            data: {
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            },
                            success: function (res) {
                                showState(
                                    '<div class="text-success mb-2"><i class="icon-check-circle-o" style="font-size:3.6rem;"></i></div>' +
                                    '<h2 class="checkout-title mb-2">Payment successful!</h2>' +
                                    '<p class="text-muted">Redirecting to your order&hellip;</p>'
                                );
                                window.location.href = res.data.redirect;
                            },
                            error: function (xhr) {
                                var res = xhr.responseJSON || {};
                                showState(
                                    '<div class="alert alert-danger">' + (res.message || 'Payment verification failed.') + '</div>' +
                                    '<a href="{{ route('checkout.index') }}" class="btn btn-outline-primary-2">Back to Checkout</a>'
                                );
                            }
                        });
                    },
                    modal: {
                        ondismiss: function () {
                            // The widget is genuinely closed at this point (not just showing
                            // an in-widget error the customer can retry past) — this is the
                            // one signal that means "they've actually stepped away", so it's
                            // the only case that triggers a "payment failed" notification.
                            $.post('{{ route('payment.razorpay.failed') }}', {
                                gateway_order_id: '{{ $payment->gateway_order_id }}',
                                reason: 'Checkout window closed by customer',
                                abandoned: 1
                            });

                            showState(
                                '<div class="alert alert-danger">Payment was not completed.</div>' +
                                '<button type="button" id="razorpay-retry-btn" class="btn btn-outline-primary-2">Try Again</button> ' +
                                '<a href="{{ route('checkout.index') }}">Back to Checkout</a>'
                            );
                        }
                    }
                };

                var rzp = new Razorpay(options);

                rzp.on('payment.failed', function (response) {
                    var reason = response.error && response.error.description ? response.error.description : 'Payment failed';

                    // Recorded for tracking, but the widget itself stays open here and lets
                    // the customer pick a different method — don't send a notification or
                    // change this page's state out from under them mid-retry. If they do
                    // give up, ondismiss (above) fires and handles both.
                    $.post('{{ route('payment.razorpay.failed') }}', {
                        gateway_order_id: '{{ $payment->gateway_order_id }}',
                        reason: reason
                    });
                });

                rzp.open();
            }

            openRazorpay();

            $(document).on('click', '#razorpay-open-btn, #razorpay-retry-btn', function () {
                showState(
                    '<div class="rz-spinner"></div>' +
                    '<h2 class="checkout-title mb-2">Opening secure payment&hellip;</h2>'
                );
                openRazorpay();
            });
        });
    </script>
@endsection
