@extends('layouts.insight')
@section('title', 'All Products')
@section('subTitle', 'All Products')

@section('style')
@endsection

@section('content')
    <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
        <div class="container">
            <h1 class="page-title">All Products</h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->
    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">All Products</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    @include('shop.partials.toolbox')
                    @include('shop.partials.product_grid', ['emptyMessage' => 'No products found.'])
                </div><!-- End .col-lg-9 -->
                <aside class="col-lg-3 order-lg-first">
                    <div class="sidebar sidebar-shop">
                        <div class="widget widget-clean">
                            <label>Filters:</label>
                            <a href="{{ url()->current() }}" class="sidebar-filter-clear">Clean All</a>
                        </div><!-- End .widget widget-clean -->

                        @if ($categories->isNotEmpty())
                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">
                                    <a data-toggle="collapse" href="#widget-categories" role="button" aria-expanded="true"
                                        aria-controls="widget-categories">
                                        Category
                                    </a>
                                </h3><!-- End .widget-title -->

                                <div class="collapse show" id="widget-categories">
                                    <div class="widget-body">
                                        <div class="filter-items filter-items-count">
                                            <div class="filter-item">
                                                <a href="{{ route('products') }}" class="text-primary">All Products</a>
                                                <span class="item-count">{{ $categories->sum('products_count') }}</span>
                                            </div><!-- End .filter-item -->

                                            @foreach ($categories as $category)
                                                <div class="filter-item">
                                                    <a href="{{ url($category->category_slug) }}">{{ $category->category_name }}</a>
                                                    <span class="item-count">{{ $category->products_count }}</span>
                                                </div><!-- End .filter-item -->
                                            @endforeach
                                        </div><!-- End .filter-items -->
                                    </div><!-- End .widget-body -->
                                </div><!-- End .collapse -->
                            </div><!-- End .widget -->
                        @endif

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
