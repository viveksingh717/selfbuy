@extends('layouts.insight')
@section('title', 'Wishlist')
@section('subTitle', 'Wishlist')

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
            <div class="container">
                <h1 class="page-title">My Wishlist</h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="cart">
                <div class="container">
                    @if ($wishlistItems->isEmpty())
                        <div class="text-center py-5">
                            <p>Your wishlist is currently empty.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    @else
                        <table class="table table-wishlist table-mobile">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock Status</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($wishlistItems as $item)
                                    @php
                                        $product = $item->product;
                                        $unitPrice = $product ? (float) $product->selling_price + (float) optional($item->productAttribute)->extra_price : 0;
                                        $inStock = $product && $product->stock_status === 'in_stock';
                                        $needsVariant = $product && !$item->product_attribute_id && $product->attributes()->exists();
                                    @endphp
                                    <tr data-wishlist-id="{{ $item->id }}">
                                        <td class="product-col">
                                            <div class="product">
                                                <figure class="product-media">
                                                    <a href="{{ $product ? route('product.details', $product->product_slug) : '#' }}">
                                                        <img src="{{ $product && $product->product_image ? asset('storage/products/thumb/'.$product->product_image) : asset('assets/images/products/product-1.jpg') }}"
                                                            alt="{{ $product->product_name ?? 'Product' }}">
                                                    </a>
                                                </figure>

                                                <h3 class="product-title">
                                                    <a href="{{ $product ? route('product.details', $product->product_slug) : '#' }}">
                                                        {{ $product->product_name ?? 'Unavailable product' }}
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
                                        <td class="price-col">
                                            @if ($product)
                                                ₹{{ number_format($unitPrice, 2) }}
                                            @else
                                                &mdash;
                                            @endif
                                        </td>
                                        <td class="stock-col">
                                            @if (!$product)
                                                <span class="text-muted">Unavailable</span>
                                            @elseif ($inStock)
                                                <span class="text-success">In Stock</span>
                                            @else
                                                <span class="text-danger">Out of Stock</span>
                                            @endif
                                        </td>
                                        <td class="action-col">
                                            @if (!$product)
                                                {{-- Nothing to do — the product was removed from the catalog. --}}
                                            @elseif ($needsVariant)
                                                <a href="{{ route('product.details', $product->product_slug) }}" class="btn btn-outline-primary-2">Select Options</a>
                                            @else
                                                <button type="button" class="btn btn-outline-primary-2 wishlist-move-to-cart-btn"
                                                    data-wishlist-id="{{ $item->id }}"
                                                    data-product-id="{{ $item->product_id }}"
                                                    data-product-attribute-id="{{ $item->product_attribute_id }}"
                                                    {{ $inStock ? '' : 'disabled' }}>
                                                    Move to Cart
                                                </button>
                                            @endif
                                        </td>
                                        <td class="remove-col">
                                            <button type="button" class="btn-remove wishlist-remove-btn"
                                                data-wishlist-id="{{ $item->id }}"><i class="icon-close"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table><!-- End .table table-wishlist -->

                        <a href="{{ route('home') }}" class="btn btn-outline-dark-2 mb-3"><span>CONTINUE SHOPPING</span><i class="icon-refresh"></i></a>
                    @endif
                </div><!-- End .container -->
            </div><!-- End .cart -->
        </div><!-- End .page-content -->
    </main><!-- End .main -->
@endsection

@section('script')
    <script>
        $(function () {
            function updateHeaderWishlistCount(count) {
                $('.wishlist-count').text(count);
            }

            $(document).on('click', '.wishlist-remove-btn', function (e) {
                e.preventDefault();
                var wishlistId = $(this).data('wishlist-id');

                Swal.fire({
                    icon: 'warning',
                    title: 'Remove item?',
                    text: 'This product will be removed from your wishlist.',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url("/wishlist/remove") }}/' + wishlistId,
                        method: 'DELETE',
                        success: function (res) {
                            updateHeaderWishlistCount(res.data.wishlist_count);
                            Swal.fire({ icon: 'success', title: 'Removed', text: res.message, timer: 1200, showConfirmButton: false })
                                .then(function () {
                                    window.location.reload();
                                });
                        },
                        error: function (xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Failed to remove product from wishlist';
                            Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                        }
                    });
                });
            });

            $(document).on('click', '.wishlist-move-to-cart-btn', function () {
                var $btn = $(this);
                var wishlistId = $btn.data('wishlist-id');

                $.ajax({
                    url: '{{ route('cart.add') }}',
                    method: 'POST',
                    data: {
                        product_id: $btn.data('product-id'),
                        product_attribute_id: $btn.data('product-attribute-id') || null,
                        qty: 1
                    },
                    success: function (res) {
                        if (!res.success) {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: res.message });
                            return;
                        }

                        $('.cart-count').text(res.data.cart_count);

                        $.ajax({
                            url: '{{ url("/wishlist/remove") }}/' + wishlistId,
                            method: 'DELETE',
                            success: function (wishlistRes) {
                                updateHeaderWishlistCount(wishlistRes.data.wishlist_count);
                                Swal.fire({ icon: 'success', title: 'Moved to cart', timer: 1200, showConfirmButton: false })
                                    .then(function () { window.location.reload(); });
                            }
                        });
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Failed to add product to cart';
                        Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                    }
                });
            });
        });
    </script>
@endsection
