@extends('layouts.insight')
@section('title', 'Cart')
@section('subTitle', 'Cart')

@section('style')
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
            <div class="container">
                <h1 class="page-title">Shopping Cart</h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="cart">
                <div class="container">
                    @if ($cartItems->isEmpty())
                        <div class="text-center py-5">
                            <p>Your cart is currently empty.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-lg-9">
                                <table class="table table-cart table-mobile">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($cartItems as $item)
                                            @php
                                                $unitPrice = (float) $item->unit_price + (float) $item->extra_price;
                                                $stock = $item->productAttribute ? $item->productAttribute->stock : ($item->product->qty ?? 0);
                                            @endphp
                                            <tr data-cart-id="{{ $item->id }}">
                                                <td class="product-col">
                                                    <div class="product">
                                                        <figure class="product-media">
                                                            <a href="{{ $item->product ? route('product.details', $item->product->product_slug) : '#' }}">
                                                                <img src="{{ $item->product && $item->product->product_image ? asset('storage/products/thumb/' . $item->product->product_image) : asset('assets/images/products/product-1.jpg') }}"
                                                                    alt="{{ $item->product->product_name ?? 'Product' }}">
                                                            </a>
                                                        </figure>

                                                        <h3 class="product-title">
                                                            <a href="{{ $item->product ? route('product.details', $item->product->product_slug) : '#' }}">
                                                                {{ $item->product->product_name ?? 'Unavailable product' }}
                                                            </a>
                                                        </h3><!-- End .product-title -->

                                                        @if ($item->color || $item->size)
                                                            <div class="product-variant text-muted small">
                                                                {{ optional($item->color)->color_name }}
                                                                {{ optional($item->size)->size_name }}
                                                            </div>
                                                        @endif
                                                    </div><!-- End .product -->
                                                </td>
                                                <td class="price-col">₹{{ number_format($unitPrice, 2) }}</td>
                                                <td class="quantity-col">
                                                    <div class="cart-product-quantity">
                                                        <input type="number" class="form-control cart-qty-input"
                                                            value="{{ $item->qty }}" min="1" max="{{ $stock }}" step="1"
                                                            data-decimals="0" data-cart-id="{{ $item->id }}" required>
                                                    </div><!-- End .cart-product-quantity -->
                                                </td>
                                                <td class="total-col cart-item-line-total">₹{{ number_format($item->line_total, 2) }}</td>
                                                <td class="remove-col">
                                                    <button type="button" class="btn-remove cart-remove-btn"
                                                        data-cart-id="{{ $item->id }}"><i class="icon-close"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table><!-- End .table table-wishlist -->
                            </div><!-- End .col-lg-9 -->
                            <aside class="col-lg-3">
                                <div class="summary summary-cart">
                                    <h3 class="summary-title">Cart Total</h3><!-- End .summary-title -->

                                    <table class="table table-summary">
                                        <tbody>
                                            <tr class="summary-subtotal">
                                                <td>Subtotal:</td>
                                                <td id="summary-subtotal">₹{{ number_format($subtotal, 2) }}</td>
                                            </tr><!-- End .summary-subtotal -->
                                            <tr class="summary-total">
                                                <td>Total:</td>
                                                <td>₹{{ number_format($subtotal, 2) }}</td>
                                            </tr><!-- End .summary-total -->
                                        </tbody>
                                    </table><!-- End .table table-summary -->
                                    <p class="text-muted small">Coupons and shipping options are available on the checkout page.</p>

                                    <a href="{{ route('checkout.index') }}" class="btn btn-outline-primary-2 btn-order btn-block">PROCEED TO CHECKOUT</a>
                                </div><!-- End .summary -->

                                <a href="{{ route('home') }}" class="btn btn-outline-dark-2 btn-block mb-3"><span>CONTINUE SHOPPING</span><i class="icon-refresh"></i></a>
                            </aside><!-- End .col-lg-3 -->
                        </div><!-- End .row -->
                    @endif
                </div><!-- End .container -->
            </div><!-- End .cart -->
        </div><!-- End .page-content -->
    </main><!-- End .main -->
@endsection

@section('script')
    <script>
        $(function () {
            function updateHeaderCartCount(count) {
                $('.cart-count').text(count);
            }

            $(document).on('change', '.cart-qty-input', function () {
                var cartId = $(this).data('cart-id');
                var qty = parseInt($(this).val(), 10) || 1;

                $.ajax({
                    url: '{{ url("/cart/update") }}/' + cartId,
                    method: 'PATCH',
                    data: { qty: qty },
                    success: function () {
                        window.location.reload();
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Failed to update cart';
                        Swal.fire({ icon: 'error', title: 'Oops...', text: msg }).then(function () {
                            window.location.reload();
                        });
                    }
                });
            });

            $(document).on('click', '.cart-remove-btn', function (e) {
                e.preventDefault();
                var cartId = $(this).data('cart-id');

                Swal.fire({
                    icon: 'warning',
                    title: 'Remove item?',
                    text: 'This product will be removed from your cart.',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url("/cart/remove") }}/' + cartId,
                        method: 'DELETE',
                        success: function (res) {
                            updateHeaderCartCount(res.data.cart_count);
                            Swal.fire({ icon: 'success', title: 'Removed', text: res.message, timer: 1200, showConfirmButton: false })
                                .then(function () {
                                    window.location.reload();
                                });
                        },
                        error: function (xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Failed to remove product from cart';
                            Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                        }
                    });
                });
            });
        });
    </script>
@endsection
