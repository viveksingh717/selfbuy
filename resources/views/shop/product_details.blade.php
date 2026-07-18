@extends('layouts.insight')
@section('title', $product->product_name)
@section('subTitle', $product->product_name)

@section('style')
@endsection

@php
    $mainImageUrl = $product->product_image
        ? asset('storage/products/' . $product->product_image)
        : asset('assets/images/products/product-1.jpg');

    $galleryImages = collect([
        (object) [
            'url' => $mainImageUrl,
            'thumb_url' => $product->product_image
                ? asset('storage/products/thumb/' . $product->product_image)
                : $mainImageUrl,
        ],
    ])->concat(
        $product->galleryImages->map(fn ($image) => (object) [
            'url' => asset('storage/products/gallery/' . $image->image_path),
            'thumb_url' => asset('storage/products/gallery/thumb/' . $image->image_path),
        ])
    );

    $availableColors = $product->attributes->pluck('color')->filter()->unique('id')->values();
    $availableSizes = $product->attributes->pluck('size')->filter()->unique('id')->values();
    $inStock = $product->stock_status === 'in_stock' && $product->qty > 0;
@endphp

@section('content')
    <main class="main">
        <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
            <div class="container d-flex align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    @if ($product->category)
                        <li class="breadcrumb-item">
                            <a href="{{ url($product->category->category_slug) }}">{{ $product->category->category_name }}</a>
                        </li>
                    @endif
                    @if ($product->subCategory)
                        <li class="breadcrumb-item">
                            <a href="{{ url($product->category->category_slug . '/' . $product->subCategory->subcategory_slug) }}">{{ $product->subCategory->subcategory_name }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="container">
                <div class="product-details-top mb-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-gallery">
                                <figure class="product-main-image">
                                    <img id="product-zoom" src="{{ $mainImageUrl }}"
                                        data-zoom-image="{{ $mainImageUrl }}" alt="{{ $product->product_name }}">

                                    <a href="#" id="btn-product-gallery" class="btn-product-gallery">
                                        <i class="icon-arrows"></i>
                                    </a>
                                </figure><!-- End .product-main-image -->

                                <div id="product-zoom-gallery" class="product-image-gallery">
                                    @foreach ($galleryImages as $index => $image)
                                        <a class="product-gallery-item {{ $index === 0 ? 'active' : '' }}" href="#"
                                            data-image="{{ $image->url }}" data-zoom-image="{{ $image->url }}">
                                            <img src="{{ $image->thumb_url }}" alt="{{ $product->product_name }}">
                                        </a>
                                    @endforeach
                                </div><!-- End .product-image-gallery -->
                            </div><!-- End .product-gallery -->
                        </div><!-- End .col-md-6 -->

                        <div class="col-md-6">
                            <div class="product-details">
                                <h1 class="product-title">{{ $product->product_name }}</h1>
                                <!-- End .product-title -->

                                <div class="product-price" id="product-price">
                                    @if ($product->discount > 0)
                                        <span class="out-price">₹{{ number_format($product->original_price, 2) }}</span>
                                        ₹{{ number_format($product->selling_price, 2) }}
                                    @else
                                        ₹{{ number_format($product->selling_price, 2) }}
                                    @endif
                                </div><!-- End .product-price -->

                                @if (!$inStock)
                                    <div class="mb-3"><span class="badge badge-danger">Out of Stock</span></div>
                                @endif

                                @if ($product->short_description)
                                    <div class="product-content">
                                        <p>{{ $product->short_description }}</p>
                                    </div><!-- End .product-content -->
                                @endif

                                @if ($availableColors->isNotEmpty())
                                    <div class="details-filter-row details-row-size">
                                        <label>Color:</label>

                                        <div class="product-nav product-nav-dots" id="color-swatches">
                                            @foreach ($availableColors as $index => $color)
                                                <a href="#" class="{{ $index === 0 ? 'active' : '' }}"
                                                    data-color-id="{{ $color->id }}"
                                                    style="background: {{ $color->color_code }};">
                                                    <span class="sr-only">{{ $color->color_name }}</span>
                                                </a>
                                            @endforeach
                                        </div><!-- End .product-nav -->
                                    </div><!-- End .details-filter-row -->
                                @endif

                                @if ($availableSizes->isNotEmpty())
                                    <div class="details-filter-row details-row-size">
                                        <label for="size">Size:</label>
                                        <div class="select-custom">
                                            <select name="size" id="size" class="form-control">
                                                <option value="" selected="selected">Select a size</option>
                                                @foreach ($availableSizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->size_name }}</option>
                                                @endforeach
                                            </select>
                                        </div><!-- End .select-custom -->
                                    </div><!-- End .details-filter-row -->
                                @endif

                                <div class="details-filter-row details-row-size">
                                    <label for="qty">Qty:</label>
                                    <div class="product-details-quantity">
                                        <input type="number" id="qty" class="form-control" value="1"
                                            min="1" max="{{ $inStock ? $product->qty : 1 }}" step="1"
                                            data-decimals="0" required {{ $inStock ? '' : 'disabled' }}>
                                    </div><!-- End .product-details-quantity -->
                                </div><!-- End .details-filter-row -->

                                <div class="product-details-action">
                                    <a href="#" class="btn-product btn-cart {{ $inStock ? '' : 'disabled' }}">
                                        <span>{{ $inStock ? 'add to cart' : 'out of stock' }}</span>
                                    </a>

                                    <div class="details-action-wrapper">
                                        <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to
                                                Wishlist</span></a>
                                    </div><!-- End .details-action-wrapper -->
                                </div><!-- End .product-details-action -->
                            </div><!-- End .product-details -->
                        </div><!-- End .col-md-6 -->
                    </div><!-- End .row -->
                </div><!-- End .product-details-top -->
            </div><!-- End .container -->

            <div class="product-details-tab product-details-extended">
                <div class="container">
                    <ul class="nav nav-pills justify-content-center" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab"
                                role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab"
                                role="tab" aria-controls="product-info-tab" aria-selected="false">Additional
                                information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-shipping-link" data-toggle="tab"
                                href="#product-shipping-tab" role="tab" aria-controls="product-shipping-tab"
                                aria-selected="false">Shipping & Returns</a>
                        </li>
                    </ul>
                </div><!-- End .container -->

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel"
                        aria-labelledby="product-desc-link">
                        <div class="product-desc-content">
                            <div class="container">
                                {!! $product->description !!}

                                @if ($product->additional_description)
                                    {!! $product->additional_description !!}
                                @endif
                            </div><!-- End .container -->
                        </div><!-- End .product-desc-content -->
                    </div><!-- .End .tab-pane -->
                    <div class="tab-pane fade" id="product-info-tab" role="tabpanel"
                        aria-labelledby="product-info-link">
                        <div class="product-desc-content">
                            <div class="container">
                                <h3>Product Information</h3>
                                <ul>
                                    <li><strong>SKU:</strong> {{ $product->sku }}</li>
                                    @if ($product->brand)
                                        <li><strong>Brand:</strong> {{ $product->brand->brand_name }}</li>
                                    @endif
                                    <li><strong>Category:</strong> {{ optional($product->subCategory)->subcategory_name ?? optional($product->category)->category_name }}</li>
                                    <li><strong>Availability:</strong> {{ $inStock ? $product->qty . ' in stock' : 'Out of stock' }}</li>
                                </ul>
                            </div><!-- End .container -->
                        </div><!-- End .product-desc-content -->
                    </div><!-- .End .tab-pane -->
                    <div class="tab-pane fade" id="product-shipping-tab" role="tabpanel"
                        aria-labelledby="product-shipping-link">
                        <div class="product-desc-content">
                            <div class="container">
                                <h3>Delivery & returns</h3>
                                <p>We deliver to over 100 countries around the world. For full details of the delivery
                                    options we offer, please view our <a href="#">Delivery information</a><br>
                                    We hope you'll love every purchase, but if you ever need to return an item you can do so
                                    within a month of receipt. For full details of how to make a return, please view our <a
                                        href="#">Returns information</a></p>
                            </div><!-- End .container -->
                        </div><!-- End .product-desc-content -->
                    </div><!-- .End .tab-pane -->
                </div><!-- End .tab-content -->
            </div><!-- End .product-details-tab -->

            @if ($relatedProducts->isNotEmpty())
                <div class="container">
                    <h2 class="title text-center mb-4">You May Also Like</h2><!-- End .title text-center -->
                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                        data-owl-options='{
                                "nav": false,
                                "dots": true,
                                "margin": 20,
                                "loop": false,
                                "responsive": {
                                    "0": {
                                        "items":1
                                    },
                                    "480": {
                                        "items":2
                                    },
                                    "768": {
                                        "items":3
                                    },
                                    "992": {
                                        "items":4
                                    },
                                    "1200": {
                                        "items":4,
                                        "nav": true,
                                        "dots": false
                                    }
                                }
                            }'>
                        @foreach ($relatedProducts as $relatedProduct)
                            <div class="product product-7">
                                <figure class="product-media">
                                    @if ($relatedProduct->stock_status !== 'in_stock')
                                        <span class="product-label label-out">Out of Stock</span>
                                    @endif
                                    <a href="{{ route('product.details', $relatedProduct->product_slug) }}">
                                        <img src="{{ $relatedProduct->product_image ? asset('storage/products/thumb/' . $relatedProduct->product_image) : asset('assets/images/products/product-1.jpg') }}"
                                            alt="{{ $relatedProduct->product_name }}" class="product-image">
                                    </a>

                                    <div class="product-action-vertical">
                                        <a href="#" class="btn-product-icon btn-wishlist btn-expandable"><span>add to
                                                wishlist</span></a>
                                    </div><!-- End .product-action-vertical -->

                                    <div class="product-action">
                                        <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                                    </div><!-- End .product-action -->
                                </figure><!-- End .product-media -->

                                <div class="product-body">
                                    <div class="product-cat">
                                        <a href="#">{{ optional($relatedProduct->subCategory)->subcategory_name ?? optional($relatedProduct->category)->category_name }}</a>
                                    </div><!-- End .product-cat -->
                                    <h3 class="product-title">
                                        <a href="{{ route('product.details', $relatedProduct->product_slug) }}">{{ $relatedProduct->product_name }}</a>
                                    </h3><!-- End .product-title -->
                                    <div class="product-price">
                                        @if ($relatedProduct->discount > 0)
                                            <span class="out-price">₹{{ number_format($relatedProduct->original_price, 2) }}</span>
                                            ₹{{ number_format($relatedProduct->selling_price, 2) }}
                                        @else
                                            ₹{{ number_format($relatedProduct->selling_price, 2) }}
                                        @endif
                                    </div><!-- End .product-price -->
                                </div><!-- End .product-body -->
                            </div><!-- End .product -->
                        @endforeach
                    </div><!-- End .owl-carousel -->
                </div><!-- End .container -->
            @endif
        </div><!-- End .page-content -->
    </main><!-- End .main -->
@endsection

@section('script')
    <script>
        $(function () {
            var variantAttributes = {!! json_encode($product->attributes->map(function ($a) {
                return [
                    'color_id' => $a->color_id,
                    'size_id' => $a->size_id,
                    'extra_price' => (float) $a->extra_price,
                    'stock' => (int) $a->stock,
                ];
            })->values()) !!};

            var baseSelling = {{ (float) $product->selling_price }};
            var baseOriginal = {{ (float) $product->original_price }};
            var hasDiscount = {{ $product->discount > 0 ? 'true' : 'false' }};

            var selectedColorId = {{ optional($availableColors->first())->id ?? 'null' }};
            var selectedSizeId = null;

            function formatPrice(n) {
                return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function findVariant() {
                if (selectedColorId === null || selectedSizeId === null) {
                    return null;
                }

                return variantAttributes.find(function (a) {
                    return a.color_id === selectedColorId && a.size_id === selectedSizeId;
                }) || null;
            }

            function updatePrice() {
                var variant = findVariant();
                var extra = variant ? variant.extra_price : 0;
                var sellingPrice = baseSelling + extra;
                var originalPrice = baseOriginal + extra;
                var priceEl = document.getElementById('product-price');

                if (hasDiscount) {
                    priceEl.innerHTML = '<span class="out-price">₹' + formatPrice(originalPrice) + '</span> ₹' + formatPrice(sellingPrice);
                } else {
                    priceEl.innerHTML = '₹' + formatPrice(sellingPrice);
                }

                var qtyInput = document.getElementById('qty');
                if (qtyInput && variant) {
                    qtyInput.max = variant.stock;
                    if (parseInt(qtyInput.value, 10) > variant.stock) {
                        qtyInput.value = variant.stock > 0 ? variant.stock : 1;
                    }
                }
            }

            document.querySelectorAll('#color-swatches a').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelectorAll('#color-swatches a').forEach(function (a) {
                        a.classList.remove('active');
                    });
                    this.classList.add('active');
                    selectedColorId = parseInt(this.dataset.colorId, 10);
                    updatePrice();
                });
            });

            var sizeSelect = document.getElementById('size');

            if (sizeSelect) {
                sizeSelect.addEventListener('change', function () {
                    selectedSizeId = this.value ? parseInt(this.value, 10) : null;
                    updatePrice();
                });
            }
        });
    </script>
@endsection
