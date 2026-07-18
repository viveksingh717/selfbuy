@extends('layouts.insight')
@section('title', 'Search results for "' . $term . '"')
@section('subTitle', 'Search results')

@section('style')
@endsection

@section('content')
    <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
        <div class="container">
            <h1 class="page-title">Search results for "{{ $term }}"</h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->
    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Search results for "{{ $term }}"</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    @include('shop.partials.toolbox')
                    @include('shop.partials.product_grid', ['emptyMessage' => 'No products found for "' . $term . '".'])
                </div><!-- End .col-lg-9 -->
                <aside class="col-lg-3 order-lg-first">
                    <div class="sidebar sidebar-shop">
                        <div class="widget widget-clean">
                            <label>Filters:</label>
                            <a href="{{ url()->current() }}?q={{ urlencode($term) }}" class="sidebar-filter-clear">Clean All</a>
                        </div><!-- End .widget widget-clean -->

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
