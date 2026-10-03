{{-- One product card on the home page (Trendy carousel, New Arrivals grid). Expects $product (with category, attributes_count). --}}
@php $productUrl = route('product.details', $product->product_slug); @endphp

<div class="product product-11 text-center">
    <figure class="product-media">
        @if ($product->stock_status !== 'in_stock')
            <span class="product-label label-out">Out of Stock</span>
        @elseif ($product->discount > 0)
            <span class="product-label label-sale">{{ rtrim(rtrim(number_format($product->discount, 2), '0'), '.') }}% off</span>
        @endif

        <a href="{{ $productUrl }}">
            <img src="{{ $product->product_image ? asset('storage/products/thumb/' . $product->product_image) : asset('assets/images/products/no-image.svg') }}"
                alt="{{ $product->product_name }}" class="product-image">
        </a>

        <div class="product-action-vertical">
            <a href="#"
                class="btn-product-icon btn-wishlist home-add-to-wishlist {{ in_array($product->id, $wishlistedProductIds ?? []) ? 'in-wishlist' : '' }}"
                data-product-id="{{ $product->id }}"><span>add to wishlist</span></a>
        </div><!-- End .product-action-vertical -->
    </figure><!-- End .product-media -->

    <div class="product-body">
        <div class="product-cat">
            <a href="{{ optional($product->category)->category_slug ? route('shop', $product->category->category_slug) : '#' }}">
                {{ optional($product->category)->category_name }}
            </a>
        </div><!-- End .product-cat -->
        <h3 class="product-title"><a href="{{ $productUrl }}">{{ $product->product_name }}</a></h3>
        <div class="product-price">
            @if ($product->discount > 0)
                <span class="out-price">₹{{ number_format($product->original_price, 2) }}</span>
            @endif
            ₹{{ number_format($product->selling_price, 2) }}
        </div><!-- End .product-price -->
    </div><!-- End .product-body -->

    <div class="product-action">
        <a href="{{ $product->attributes_count > 0 ? $productUrl : '#' }}"
            class="btn-product btn-cart home-add-to-cart {{ $product->stock_status !== 'in_stock' ? 'disabled' : '' }}"
            data-product-id="{{ $product->id }}"
            data-has-variant="{{ $product->attributes_count > 0 ? 1 : 0 }}">
            <span>{{ $product->attributes_count > 0 ? 'select options' : 'add to cart' }}</span>
        </a>
    </div><!-- End .product-action -->
</div><!-- End .product -->
