@extends('layouts.insight')
@section('title', 'Order Confirmed')
@section('subTitle', 'Order Confirmed')

@section('style')
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
            <div class="container">
                <h1 class="page-title">Order Confirmed</h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order Confirmed</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="container">
                <div class="text-center mb-4">
                    <h2>Thank you, {{ $order->first_name }}!</h2>
                    <p>Your order has been placed successfully. A confirmation has been recorded for order
                        <strong>{{ $order->order_number }}</strong>.</p>
                </div>

                {{-- Order journey: Placed -> Processing -> Shipped -> Delivered (updated from the admin panel) --}}
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8">
                        <div class="border rounded p-3 p-md-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:8px">
                                <h5 class="mb-0">Track your order</h5>
                                <div class="d-flex flex-wrap align-items-center" style="gap:10px">
                                    <small class="text-muted">Payment: {{ ucfirst($order->payment_status) }} ({{ $order->paymentMethodLabel() }})</small>
                                    {{-- signed: works even when this page was opened from an email without logging in --}}
                                    <a href="{{ $order->invoiceUrl() }}" class="btn btn-outline-primary-2 btn-sm"><i class="icon-long-arrow-down"></i><span>DOWNLOAD INVOICE</span></a>
                                </div>
                            </div>
                            @include('partials.order_journey', ['order' => $order])
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <table class="table table-cart table-mobile">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    @auth
                                        <th></th>
                                    @endauth
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product_name }}
                                            @if ($item->variant_label)
                                                <br><small class="text-muted">{{ $item->variant_label }}</small>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format((float) $item->unit_price + (float) $item->extra_price, 2) }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>₹{{ number_format($item->line_total, 2) }}</td>
                                        @auth
                                            <td>
                                                @if ($item->product)
                                                    <a href="{{ route('product.details', $item->product->product_slug) }}#product-review-tab" class="btn btn-outline-primary-2 btn-sm">Write a Review</a>
                                                @endif
                                            </td>
                                        @endauth
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-6">
                                <h4>Shipping Address</h4>
                                <p>
                                    {{ $order->first_name }} {{ $order->last_name }}<br>
                                    {{ $order->address_line1 }}<br>
                                    @if ($order->address_line2)
                                        {{ $order->address_line2 }}<br>
                                    @endif
                                    {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                                    {{ $order->country }}<br>
                                    {{ $order->phone }}<br>
                                    {{ $order->email }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h4>Order Summary</h4>
                                <table class="table table-summary">
                                    <tbody>
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                        </tr>
                                        @if ($order->discount > 0)
                                            <tr>
                                                <td>Discount ({{ $order->coupon_code }}):</td>
                                                <td>-₹{{ number_format($order->discount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>Shipping ({{ ucfirst($order->shipping_method) }}):</td>
                                            <td>₹{{ number_format($order->shipping_cost, 2) }}</td>
                                        </tr>
                                        <tr class="summary-total">
                                            <td>Total:</td>
                                            <td>₹{{ number_format($order->total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Method:</td>
                                            <td>{{ $order->paymentMethodLabel() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div><!-- End .col-lg-8 -->
                </div><!-- End .row -->
            </div><!-- End .container -->
        </div><!-- End .page-content -->
    </main><!-- End .main -->
@endsection

@section('script')
@endsection
