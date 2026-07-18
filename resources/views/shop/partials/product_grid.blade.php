<div class="products mb-3">
    <div class="row justify-content-center">
        @forelse ($products as $product)
            <div class="col-6 col-md-4 col-lg-4">
                <div class="product product-7 text-center">
                    <figure class="product-media">
                        @if ($product->stock_status !== 'in_stock')
                            <span class="product-label label-out">Out of Stock</span>
                        @endif
                        <a href="{{ route('product.details', $product->product_slug) }}">
                            <img src="{{ $product->product_image ? asset('storage/products/thumb/' . $product->product_image) : asset('assets/images/products/product-1.jpg') }}"
                                alt="{{ $product->product_name }}" class="product-image">
                        </a>

                        <div class="product-action-vertical">
                            <a href="#"
                                class="btn-product-icon btn-wishlist btn-expandable"><span>add
                                    to
                                    wishlist</span></a>
                        </div>

                        <div class="product-action">
                            <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                        </div>
                    </figure>

                    <div class="product-body">
                        <div class="product-cat">
                            <a
                                href="#">{{ optional($product->subCategory)->subcategory_name ?? optional($product->category)->category_name }}</a>
                        </div><!-- End .product-cat -->
                        <h3 class="product-title"><a
                                href="{{ route('product.details', $product->product_slug) }}">{{ $product->product_name }}</a>
                        </h3><!-- End .product-title -->
                        <div class="product-price">
                            @if ($product->discount > 0)
                                <span
                                    class="out-price">₹{{ number_format($product->original_price, 2) }}</span>
                                ₹{{ number_format($product->selling_price, 2) }}
                            @else
                                ₹{{ number_format($product->selling_price, 2) }}
                            @endif
                        </div><!-- End .product-price -->
                    </div><!-- End .product-body -->
                </div><!-- End .product -->
            </div><!-- End .col-sm-6 col-lg-4 -->
        @empty
            <div class="col-12 text-center py-5">
                <p>{{ $emptyMessage ?? 'No products found.' }}</p>
            </div>
        @endforelse
    </div><!-- End .row -->
</div><!-- End .products -->

@if ($products->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link page-link-prev"
                    href="{{ $products->onFirstPage() ? '#' : $products->previousPageUrl() }}"
                    aria-label="Previous"
                    @if ($products->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                    <span aria-hidden="true"><i class="icon-long-arrow-left"></i></span>Prev
                </a>
            </li>
            @for ($i = 1; $i <= $products->lastPage(); $i++)
                <li class="page-item {{ $products->currentPage() == $i ? 'active' : '' }}"
                    @if ($products->currentPage() == $i) aria-current="page" @endif>
                    <a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a>
                </li>
            @endfor
            <li class="page-item-total">of {{ $products->lastPage() }}</li>
            <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link page-link-next"
                    href="{{ $products->hasMorePages() ? $products->nextPageUrl() : '#' }}"
                    aria-label="Next">
                    Next <span aria-hidden="true"><i class="icon-long-arrow-right"></i></span>
                </a>
            </li>
        </ul>
    </nav>
@endif
