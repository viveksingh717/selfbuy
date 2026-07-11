@extends('layouts.insight')
@section('title', !empty($subcategory) ? $subcategory->subcategory_name : $category->category_name)
@section('subTitle', !empty($subcategory) ? $subcategory->subcategory_name : $category->category_name)

@section('style')
@endsection

@section('content')
    <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
        <div class="container">
            <h1 class="page-title">{{ !empty($subcategory) ? $subcategory->subcategory_name : $category->category_name }}
            </h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->
    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Shop</a></li>

                @if (!empty($subcategory))
                    <li class="breadcrumb-item"><a
                            href="{{ url($category->category_slug) }}">{{ $category->category_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ !empty($subcategory) ? $subcategory->subcategory_name : $category->category_name }}</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->category_name }}</li>
                @endif
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="toolbox">
                        <div class="toolbox-left">
                            <div class="toolbox-info">
                                Showing <span>{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of
                                    {{ $products->total() }}</span> Products
                            </div><!-- End .toolbox-info -->
                        </div><!-- End .toolbox-left -->

                        <div class="toolbox-right">
                            <div class="toolbox-sort">
                                <label for="sortby">Sort by:</label>
                                <div class="select-custom">
                                    <select name="sort" id="sortby" class="form-control">
                                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
                                        <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: Low
                                            to High</option>
                                        <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price:
                                            High to Low</option>
                                        <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name: A to Z
                                        </option>
                                    </select>
                                </div>
                            </div><!-- End .toolbox-sort -->
                            <div class="toolbox-layout">
                                <a href="category-list.html" class="btn-layout">
                                    <svg width="16" height="10">
                                        <rect x="0" y="0" width="4" height="4" />
                                        <rect x="6" y="0" width="10" height="4" />
                                        <rect x="0" y="6" width="4" height="4" />
                                        <rect x="6" y="6" width="10" height="4" />
                                    </svg>
                                </a>

                                <a href="category-2cols.html" class="btn-layout">
                                    <svg width="10" height="10">
                                        <rect x="0" y="0" width="4" height="4" />
                                        <rect x="6" y="0" width="4" height="4" />
                                        <rect x="0" y="6" width="4" height="4" />
                                        <rect x="6" y="6" width="4" height="4" />
                                    </svg>
                                </a>

                                <a href="category.html" class="btn-layout active">
                                    <svg width="16" height="10">
                                        <rect x="0" y="0" width="4" height="4" />
                                        <rect x="6" y="0" width="4" height="4" />
                                        <rect x="12" y="0" width="4" height="4" />
                                        <rect x="0" y="6" width="4" height="4" />
                                        <rect x="6" y="6" width="4" height="4" />
                                        <rect x="12" y="6" width="4" height="4" />
                                    </svg>
                                </a>
                            </div><!-- End .toolbox-layout -->
                        </div><!-- End .toolbox-right -->
                    </div><!-- End .toolbox -->

                    <div class="products mb-3">
                        <div class="row justify-content-center">
                            @forelse ($products as $product)
                                <div class="col-6 col-md-4 col-lg-4">
                                    <div class="product product-7 text-center">
                                        <figure class="product-media">
                                            @if ($product->stock_status !== 'in_stock')
                                                <span class="product-label label-out">Out of Stock</span>
                                            @endif
                                            <a href="#">
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
                                            <h3 class="product-title"><a href="#">{{ $product->product_name }}</a>
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
                                    <p>No products found in this category.</p>
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
                </div><!-- End .col-lg-9 -->
                <aside class="col-lg-3 order-lg-first">
                    @php
                        $filterQueryString = request()->except('page')
                            ? '?' . http_build_query(request()->except('page'))
                            : '';
                    @endphp
                    <div class="sidebar sidebar-shop">
                        <div class="widget widget-clean">
                            <label>Filters:</label>
                            <a href="{{ url()->current() }}" class="sidebar-filter-clear">Clean All</a>
                        </div><!-- End .widget widget-clean -->

                        <div class="widget widget-collapsible">
                            <h3 class="widget-title">
                                <a data-toggle="collapse" href="#widget-1" role="button" aria-expanded="true"
                                    aria-controls="widget-1">
                                    Category
                                </a>
                            </h3><!-- End .widget-title -->

                            <div class="collapse show" id="widget-1">
                                <div class="widget-body">
                                    <div class="filter-items filter-items-count">
                                        <div class="filter-item">
                                            <a href="{{ url($category->category_slug) . $filterQueryString }}"
                                                class="{{ empty($subcategory) ? 'text-primary' : '' }}">
                                                All {{ $category->category_name }}
                                            </a>
                                            <span class="item-count">{{ $subcategories->sum('products_count') }}</span>
                                        </div><!-- End .filter-item -->

                                        @foreach ($subcategories as $subCat)
                                            <div class="filter-item">
                                                <a href="{{ url($category->category_slug . '/' . $subCat->subcategory_slug) . $filterQueryString }}"
                                                    class="{{ !empty($subcategory) && $subcategory->id === $subCat->id ? 'text-primary' : '' }}">
                                                    {{ $subCat->subcategory_name }}
                                                </a>
                                                <span class="item-count">{{ $subCat->products_count }}</span>
                                            </div><!-- End .filter-item -->
                                        @endforeach
                                    </div><!-- End .filter-items -->
                                </div><!-- End .widget-body -->
                            </div><!-- End .collapse -->
                        </div><!-- End .widget -->

                        <form method="GET" action="{{ url()->current() }}">
                            @foreach ($selectedColorIds as $colorId)
                                <input type="hidden" name="color_id[]" value="{{ $colorId }}">
                            @endforeach

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-2" role="button" aria-expanded="true"
                                        aria-controls="widget-2">
                                        Size
                                    </a>
                                </h3><!-- End .widget-title -->

                                <div class="collapse show" id="widget-2">
                                    <div class="widget-body">
                                        <div class="filter-items">
                                            @forelse ($sizes as $size)
                                                <div class="filter-item">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="size-{{ $size->id }}" name="size_id[]"
                                                            value="{{ $size->id }}"
                                                            {{ in_array($size->id, $selectedSizeIds) ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="size-{{ $size->id }}">{{ $size->size_code }}</label>
                                                    </div><!-- End .custom-checkbox -->
                                                </div><!-- End .filter-item -->
                                            @empty
                                                <p class="mb-0">No sizes available.</p>
                                            @endforelse
                                        </div><!-- End .filter-items -->
                                    </div><!-- End .widget-body -->
                                </div><!-- End .collapse -->
                            </div><!-- End .widget -->

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-3" role="button" aria-expanded="true"
                                        aria-controls="widget-3">
                                        Colour
                                    </a>
                                </h3><!-- End .widget-title -->

                                <div class="collapse show" id="widget-3">
                                    <div class="widget-body">
                                        <div class="filter-colors">
                                            @forelse ($colors as $color)
                                                @php
                                                    $isColorSelected = in_array($color->id, $selectedColorIds);
                                                    $toggledColorIds = $isColorSelected
                                                        ? array_values(array_diff($selectedColorIds, [$color->id]))
                                                        : array_merge($selectedColorIds, [$color->id]);
                                                @endphp
                                                <a href="{{ request()->fullUrlWithQuery(['color_id' => $toggledColorIds, 'page' => null]) }}"
                                                    class="{{ $isColorSelected ? 'selected' : '' }}"
                                                    style="background: {{ $color->color_code }};">
                                                    <span class="sr-only">{{ $color->color_name }}</span>
                                                </a>
                                            @empty
                                                <p class="mb-0">No colours available.</p>
                                            @endforelse
                                        </div><!-- End .filter-colors -->
                                    </div><!-- End .widget-body -->
                                </div><!-- End .collapse -->
                            </div><!-- End .widget -->

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-4" role="button" aria-expanded="true"
                                        aria-controls="widget-4">
                                        Brand
                                    </a>
                                </h3><!-- End .widget-title -->

                                <div class="collapse show" id="widget-4">
                                    <div class="widget-body">
                                        <div class="filter-items">
                                            @forelse ($brands as $brand)
                                                <div class="filter-item">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="brand-{{ $brand->id }}" name="brand_id[]"
                                                            value="{{ $brand->id }}"
                                                            {{ in_array($brand->id, $selectedBrandIds) ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="brand-{{ $brand->id }}">{{ $brand->brand_name }}</label>
                                                    </div><!-- End .custom-checkbox -->
                                                </div><!-- End .filter-item -->
                                            @empty
                                                <p class="mb-0">No brands available.</p>
                                            @endforelse
                                        </div><!-- End .filter-items -->
                                    </div><!-- End .widget-body -->
                                </div><!-- End .collapse -->
                            </div><!-- End .widget -->

                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true"
                                        aria-controls="widget-5">
                                        Price
                                    </a>
                                </h3><!-- End .widget-title -->

                                <div class="collapse show" id="widget-5">
                                    <div class="widget-body">
                                        <div class="filter-price">
                                            <div class="filter-price-text">
                                                Price Range:
                                                <span id="filter-price-range"></span>
                                            </div><!-- End .filter-price-text -->

                                            <div id="price-slider"></div><!-- End #price-slider -->

                                            <input type="hidden" name="min_price" id="min_price"
                                                value="{{ $minPrice ?: 10 }}">
                                            <input type="hidden" name="max_price" id="max_price"
                                                value="{{ $maxPrice ?: 100000 }}">
                                        </div><!-- End .filter-price -->
                                    </div><!-- End .widget-body -->
                                </div><!-- End .collapse -->
                            </div><!-- End .widget -->

                            <button type="submit" class="btn btn-outline-primary-2 btn-block">
                                <span>Apply Filters</span>
                            </button>
                        </form>
                    </div><!-- End .sidebar sidebar-shop -->
                </aside><!-- End .col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
@endsection

@section('script')
    <script>
        $(function() {
            document.getElementById('sortby').addEventListener('change', function() {
                var url = new URL(window.location.href);
                url.searchParams.set('sort', this.value);
                url.searchParams.delete('page');
                window.location.href = url.toString();
            });

            if (typeof noUiSlider === 'object') {
                var priceSlider = document.getElementById('price-slider');

                if (priceSlider && priceSlider.noUiSlider) {
                    priceSlider.noUiSlider.updateOptions({
                        start: [{{ (int) ($minPrice ?: 10) }}, {{ (int) ($maxPrice ?: 100000) }}],
                        range: {
                            min: 10,
                            max: 100000
                        },
                        step: 100,
                        margin: 100,
                        format: wNumb({
                            decimals: 0,
                            prefix: '₹'
                        })
                    });

                    priceSlider.noUiSlider.on('change', function(values) {
                        var min = parseInt(values[0], 10);
                        var max = parseInt(values[1], 10);

                        if (!isNaN(min)) {
                            document.getElementById('min_price').value = min;
                        }

                        if (!isNaN(max)) {
                            document.getElementById('max_price').value = max;
                        }
                    });
                }
            }
        });
    </script>
@endsection
