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
                    @include('shop.partials.toolbox')
                    @include('shop.partials.product_grid', ['emptyMessage' => 'No products found in this category.'])
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

                        @include('shop.partials.filters_form')
                    </div><!-- End .sidebar sidebar-shop -->
                </aside><!-- End .col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
@endsection

@section('script')
    @include('shop.partials.filters_script')
@endsection
