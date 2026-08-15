<div class="dropdown-cart-products">
    @forelse (($headerCartItems ?? []) as $item)
        <div class="product">
            <div class="product-cart-details">
                <h4 class="product-title">
                    <a href="{{ $item->product ? route('product.details', $item->product->product_slug) : '#' }}">
                        {{ $item->product->product_name ?? 'Unavailable product' }}
                    </a>
                </h4>

                <span class="cart-product-info">
                    <span class="cart-product-qty">{{ $item->qty }}</span>
                    x ₹{{ number_format((float) $item->unit_price + (float) $item->extra_price, 2) }}
                </span>
            </div><!-- End .product-cart-details -->

            <figure class="product-image-container">
                <a href="{{ $item->product ? route('product.details', $item->product->product_slug) : '#' }}"
                    class="product-image">
                    <img src="{{ $item->product && $item->product->product_image ? asset('storage/products/thumb/' . $item->product->product_image) : asset('assets/images/products/product-1.jpg') }}"
                        alt="product">
                </a>
            </figure>
            <a href="{{ route('cart.index') }}" class="btn-remove" title="Remove Product"><i
                    class="icon-close"></i></a>
        </div><!-- End .product -->
    @empty
        <p class="text-center mb-0 p-2">Your cart is empty.</p>
    @endforelse
</div><!-- End .cart-product -->

@if (!empty($headerCartItems) && $headerCartItems->isNotEmpty())
    <div class="dropdown-cart-total">
        <span>Total</span>

        <span class="cart-total-price">₹{{ number_format($headerCartTotal ?? 0, 2) }}</span>
    </div><!-- End .dropdown-cart-total -->

    <div class="dropdown-cart-action">
        <a href="{{ route('cart.index') }}" class="btn btn-primary">View Cart</a>
    </div><!-- End .dropdown-cart-total -->
@endif
