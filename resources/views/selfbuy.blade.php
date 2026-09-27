@extends('layouts.insight')
@section('title', 'SelfBuy')
@section('subTitle', 'SelfBuy')

@section('style')
    <style>
        .home-category-banner img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; }
    </style>
@endsection

@section('content')
    <div class="intro-section bg-lighter pt-5 pb-6">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="intro-slider-container slider-container-ratio slider-container-1 mb-2 mb-lg-0">
                        <div class="intro-slider intro-slider-1 owl-carousel owl-simple owl-light owl-nav-inside"
                            data-toggle="owl"
                            data-owl-options='{
                                "nav": false,
                                "responsive": {
                                    "768": {
                                        "nav": true
                                    }
                                }
                            }'>
                            @foreach (home_slides() as $slide)
                                <div class="intro-slide">
                                    <figure class="slide-image">
                                        <picture>
                                            <source media="(max-width: 480px)" srcset="{{ $slide->image_mobile_url }}">
                                            <img src="{{ $slide->image_url }}" alt="{{ str_replace("\n", ' ', $slide->title ?: 'Slide') }}">
                                        </picture>
                                    </figure><!-- End .slide-image -->

                                    <div class="intro-content">
                                        @if ($slide->subtitle)
                                            <h3 class="intro-subtitle">{{ $slide->subtitle }}</h3>
                                        @endif
                                        @if ($slide->title)
                                            <h1 class="intro-title">{!! nl2br(e($slide->title)) !!}</h1>
                                        @endif
                                        @if ($slide->description)
                                            <p class="intro-text text-white mb-2">{{ $slide->description }}</p>
                                        @endif

                                        @if ($slide->button_text)
                                            <a href="{{ home_link($slide->button_link) }}" class="btn btn-outline-white">
                                                <span>{{ $slide->button_text }}</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div><!-- End .intro-content -->
                                </div><!-- End .intro-slide -->
                            @endforeach
                        </div><!-- End .intro-slider owl-carousel owl-simple -->

                        <span class="slider-loader"></span><!-- End .slider-loader -->
                    </div><!-- End .intro-slider-container -->
                </div><!-- End .col-lg-8 -->
                <div class="col-lg-4">
                    <div class="intro-banners">
                        <div class="row row-sm">
                            <div class="col-md-6 col-lg-12">
                                <div class="banner banner-display">
                                    <a href="#">
                                        <img src="{{ asset('assets/images/banners/home/intro/banner-1.jpg') }}"
                                            alt="Banner">
                                    </a>

                                    <div class="banner-content">
                                        <h4 class="banner-subtitle text-darkwhite"><a href="#">Clearence</a></h4>
                                        <!-- End .banner-subtitle -->
                                        <h3 class="banner-title text-white"><a href="#">Chairs & Chaises
                                                <br>Up to 40% off</a></h3><!-- End .banner-title -->
                                        <a href="#" class="btn btn-outline-white banner-link">Shop Now<i
                                                class="icon-long-arrow-right"></i></a>
                                    </div><!-- End .banner-content -->
                                </div><!-- End .banner -->
                            </div><!-- End .col-md-6 col-lg-12 -->

                            <div class="col-md-6 col-lg-12">
                                <div class="banner banner-display mb-0">
                                    <a href="#">
                                        <img src="{{ asset('assets/images/banners/home/intro/banner-2.jpg') }}"
                                            alt="Banner">
                                    </a>

                                    <div class="banner-content">
                                        <h4 class="banner-subtitle text-darkwhite"><a href="#">New
                                                in</a></h4><!-- End .banner-subtitle -->
                                        <h3 class="banner-title text-white"><a href="#">Best Lighting
                                                <br>Collection</a></h3><!-- End .banner-title -->
                                        <a href="#" class="btn btn-outline-white banner-link">Discover
                                            Now<i class="icon-long-arrow-right"></i></a>
                                    </div><!-- End .banner-content -->
                                </div><!-- End .banner -->
                            </div><!-- End .col-md-6 col-lg-12 -->
                        </div><!-- End .row row-sm -->
                    </div><!-- End .intro-banners -->
                </div><!-- End .col-lg-4 -->
            </div><!-- End .row -->

            <div class="mb-6"></div><!-- End .mb-6 -->

            @php $partners = home_partners(); @endphp
            @if ($partners->isNotEmpty())
                <div class="owl-carousel owl-simple" data-toggle="owl"
                    data-owl-options='{
                        "nav": false,
                        "dots": false,
                        "margin": 30,
                        "loop": false,
                        "responsive": {
                            "0": {
                                "items":2
                            },
                            "420": {
                                "items":3
                            },
                            "600": {
                                "items":4
                            },
                            "900": {
                                "items":5
                            },
                            "1024": {
                                "items":6
                            }
                        }
                    }'>
                    @foreach ($partners as $partner)
                        <a href="{{ home_link($partner->link) }}" class="brand" title="{{ $partner->name }}"
                            @if (preg_match('#^(https?:)?//#i', (string) $partner->link)) target="_blank" rel="noopener" @endif>
                            <img src="{{ $partner->image_url }}" alt="{{ $partner->name ?: 'Partner' }}">
                        </a>
                    @endforeach
                </div><!-- End .owl-carousel -->
            @endif
        </div><!-- End .container -->
    </div><!-- End .bg-lighter -->

    <div class="mb-6"></div><!-- End .mb-6 -->

    @if ($trendyProducts->isNotEmpty())
        <div class="container">
            <div class="heading heading-center mb-3">
                <h2 class="title-lg">{{ home_setting('trendy_products_title') }}</h2><!-- End .title -->

                <ul class="nav nav-pills justify-content-center" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="trendy-all-link" data-toggle="tab" href="#trendy-all-tab"
                            role="tab" aria-controls="trendy-all-tab" aria-selected="true">All</a>
                    </li>
                    @foreach ($trendyCategories as $category)
                        <li class="nav-item">
                            <a class="nav-link" id="trendy-cat-{{ $category->id }}-link" data-toggle="tab"
                                href="#trendy-cat-{{ $category->id }}-tab" role="tab"
                                aria-controls="trendy-cat-{{ $category->id }}-tab" aria-selected="false">{{ $category->category_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div><!-- End .heading -->

            <div class="tab-content tab-content-carousel">
                <div class="tab-pane p-0 fade show active" id="trendy-all-tab" role="tabpanel" aria-labelledby="trendy-all-link">
                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                        data-owl-options='{
                            "nav": false,
                            "dots": true,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": { "items": 2 },
                                "480": { "items": 2 },
                                "768": { "items": 3 },
                                "992": { "items": 4 },
                                "1200": { "items": 4, "nav": true, "dots": false }
                            }
                        }'>
                        @foreach ($trendyProducts as $product)
                            @include('partials.home.product_card')
                        @endforeach
                    </div><!-- End .owl-carousel -->
                </div><!-- .End .tab-pane -->

                @foreach ($trendyCategories as $category)
                    <div class="tab-pane p-0 fade" id="trendy-cat-{{ $category->id }}-tab" role="tabpanel"
                        aria-labelledby="trendy-cat-{{ $category->id }}-link">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                            data-owl-options='{
                            "nav": false,
                            "dots": true,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": { "items": 2 },
                                "480": { "items": 2 },
                                "768": { "items": 3 },
                                "992": { "items": 4 },
                                "1200": { "items": 4, "nav": true, "dots": false }
                            }
                        }'>
                            @foreach ($trendyProducts->where('category_id', $category->id) as $product)
                                @include('partials.home.product_card')
                            @endforeach
                        </div><!-- End .owl-carousel -->
                    </div><!-- .End .tab-pane -->
                @endforeach
            </div><!-- End .tab-content -->
        </div><!-- End .container -->
    @endif

    @if ($homeCategories->isNotEmpty())
        <div class="container categories pt-6">
            <h2 class="title-lg text-center mb-4">{{ home_setting('shop_by_category_title') }}</h2><!-- End .title-lg text-center -->

            <div class="row justify-content-center">
                @foreach ($homeCategories as $category)
                    @php $categoryUrl = route('shop', $category->category_slug); @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="banner banner-display banner-link-anim home-category-banner">
                            <a href="{{ $categoryUrl }}">
                                <img src="{{ $category->category_image ? asset('storage/category/' . $category->category_image) : asset('assets/images/banners/home/banner-1.jpg') }}"
                                    alt="{{ $category->category_name }}">
                            </a>

                            <div class="banner-content banner-content-center">
                                <h3 class="banner-title text-white"><a href="{{ $categoryUrl }}">{{ $category->category_name }}</a></h3>
                                <!-- End .banner-title -->
                                <a href="{{ $categoryUrl }}" class="btn btn-outline-white banner-link">Shop Now<i
                                        class="icon-long-arrow-right"></i></a>
                            </div><!-- End .banner-content -->
                        </div><!-- End .banner -->
                    </div>
                @endforeach
            </div><!-- End .row -->
        </div><!-- End .container -->
    @endif

    <div class="mb-5"></div><!-- End .mb-6 -->


    @if ($newArrivals->isNotEmpty())
        <div class="container">
            <div class="heading heading-center mb-6">
                <h2 class="title">{{ home_setting('new_arrivals_title') }}</h2><!-- End .title -->

                <ul class="nav nav-pills nav-border-anim justify-content-center" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="new-all-link" data-toggle="tab" href="#new-all-tab" role="tab"
                            aria-controls="new-all-tab" aria-selected="true">All</a>
                    </li>
                    @foreach ($newArrivalCategories as $category)
                        <li class="nav-item">
                            <a class="nav-link" id="new-cat-{{ $category->id }}-link" data-toggle="tab"
                                href="#new-cat-{{ $category->id }}-tab" role="tab"
                                aria-controls="new-cat-{{ $category->id }}-tab" aria-selected="false">{{ $category->category_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div><!-- End .heading -->

            <div class="tab-content">
                <div class="tab-pane p-0 fade show active" id="new-all-tab" role="tabpanel" aria-labelledby="new-all-link">
                    <div class="products">
                        <div class="row justify-content-center">
                            @foreach ($newArrivals->take(8) as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('partials.home.product_card')
                                </div>
                            @endforeach
                        </div><!-- End .row -->
                    </div><!-- End .products -->

                    <div class="more-container text-center">
                        <a href="{{ route('products') }}" class="btn btn-outline-darker btn-more"><span>Load more products</span><i
                                class="icon-long-arrow-right"></i></a>
                    </div><!-- End .more-container -->
                </div><!-- .End .tab-pane -->

                @foreach ($newArrivalCategories as $category)
                    <div class="tab-pane p-0 fade" id="new-cat-{{ $category->id }}-tab" role="tabpanel"
                        aria-labelledby="new-cat-{{ $category->id }}-link">
                        <div class="products">
                            <div class="row justify-content-center">
                                @foreach ($newArrivals->where('category_id', $category->id)->take(8) as $product)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        @include('partials.home.product_card')
                                    </div>
                                @endforeach
                            </div><!-- End .row -->
                        </div><!-- End .products -->

                        <div class="more-container text-center">
                            <a href="{{ route('shop', $category->category_slug) }}" class="btn btn-outline-darker btn-more"><span>Load more products</span><i
                                    class="icon-long-arrow-right"></i></a>
                        </div><!-- End .more-container -->
                    </div><!-- .End .tab-pane -->
                @endforeach
            </div><!-- End .tab-content -->
        </div><!-- End .container -->
    @endif

    <div class="container">
        <hr>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-sm-6">
                <div class="icon-box icon-box-card text-center">
                    <span class="icon-box-icon">
                        <i class="icon-rocket"></i>
                    </span>
                    <div class="icon-box-content">
                        <h3 class="icon-box-title">Payment & Delivery</h3><!-- End .icon-box-title -->
                        <p>Free shipping for orders over $50</p>
                    </div><!-- End .icon-box-content -->
                </div><!-- End .icon-box -->
            </div><!-- End .col-lg-4 col-sm-6 -->

            <div class="col-lg-4 col-sm-6">
                <div class="icon-box icon-box-card text-center">
                    <span class="icon-box-icon">
                        <i class="icon-rotate-left"></i>
                    </span>
                    <div class="icon-box-content">
                        <h3 class="icon-box-title">Return & Refund</h3><!-- End .icon-box-title -->
                        <p>Free 100% money back guarantee</p>
                    </div><!-- End .icon-box-content -->
                </div><!-- End .icon-box -->
            </div><!-- End .col-lg-4 col-sm-6 -->

            <div class="col-lg-4 col-sm-6">
                <div class="icon-box icon-box-card text-center">
                    <span class="icon-box-icon">
                        <i class="icon-life-ring"></i>
                    </span>
                    <div class="icon-box-content">
                        <h3 class="icon-box-title">Quality Support</h3><!-- End .icon-box-title -->
                        <p>Alway online feedback 24/7</p>
                    </div><!-- End .icon-box-content -->
                </div><!-- End .icon-box -->
            </div><!-- End .col-lg-4 col-sm-6 -->
        </div><!-- End .row -->

        <div class="mb-2"></div><!-- End .mb-2 -->
    </div><!-- End .container -->
    <div class="blog-posts pt-7 pb-7" style="background-color: #fafafa;">
        <div class="container">
            <h2 class="title-lg text-center mb-3 mb-md-4">From Our Blog</h2><!-- End .title-lg text-center -->

            <div class="owl-carousel owl-simple carousel-with-shadow" data-toggle="owl"
                data-owl-options='{
                    "nav": false,
                    "dots": true,
                    "items": 3,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":1
                        },
                        "600": {
                            "items":2
                        },
                        "992": {
                            "items":3
                        }
                    }
                }'>
                <article class="entry entry-display">
                    <figure class="entry-media">
                        <a href="single.html">
                            <img src="assets/images/blog/home/post-1.jpg" alt="image desc">
                        </a>
                    </figure><!-- End .entry-media -->

                    <div class="entry-body pb-4 text-center">
                        <div class="entry-meta">
                            <a href="#">Nov 22, 2018</a>, 0 Comments
                        </div><!-- End .entry-meta -->

                        <h3 class="entry-title">
                            <a href="single.html">Sed adipiscing ornare.</a>
                        </h3><!-- End .entry-title -->

                        <div class="entry-content">
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus
                                hendrerit.<br>Pelletesque aliquet nibh necurna. </p>
                            <a href="single.html" class="read-more">Read More</a>
                        </div><!-- End .entry-content -->
                    </div><!-- End .entry-body -->
                </article><!-- End .entry -->

                <article class="entry entry-display">
                    <figure class="entry-media">
                        <a href="single.html">
                            <img src="assets/images/blog/home/post-2.jpg" alt="image desc">
                        </a>
                    </figure><!-- End .entry-media -->

                    <div class="entry-body pb-4 text-center">
                        <div class="entry-meta">
                            <a href="#">Dec 12, 2018</a>, 0 Comments
                        </div><!-- End .entry-meta -->

                        <h3 class="entry-title">
                            <a href="single.html">Fusce lacinia arcuet nulla.</a>
                        </h3><!-- End .entry-title -->

                        <div class="entry-content">
                            <p>Sed pretium, ligula sollicitudin laoreet<br>viverra, tortor libero sodales leo,
                                eget blandit nunc tortor eu nibh. Nullam mollis justo. </p>
                            <a href="single.html" class="read-more">Read More</a>
                        </div><!-- End .entry-content -->
                    </div><!-- End .entry-body -->
                </article><!-- End .entry -->

                <article class="entry entry-display">
                    <figure class="entry-media">
                        <a href="single.html">
                            <img src="assets/images/blog/home/post-3.jpg" alt="image desc">
                        </a>
                    </figure><!-- End .entry-media -->

                    <div class="entry-body pb-4 text-center">
                        <div class="entry-meta">
                            <a href="#">Dec 19, 2018</a>, 2 Comments
                        </div><!-- End .entry-meta -->

                        <h3 class="entry-title">
                            <a href="single.html">Quisque volutpat mattis eros.</a>
                        </h3><!-- End .entry-title -->

                        <div class="entry-content">
                            <p>Suspendisse potenti. Sed egestas, ante et vulputate volutpat, eros pede semper
                                est, vitae luctus metus libero eu augue. </p>
                            <a href="single.html" class="read-more">Read More</a>
                        </div><!-- End .entry-content -->
                    </div><!-- End .entry-body -->
                </article><!-- End .entry -->
            </div><!-- End .owl-carousel -->
        </div><!-- container -->

        <div class="more-container text-center mb-0 mt-3">
            <a href="blog.html" class="btn btn-outline-darker btn-more"><span>View more articles</span><i
                    class="icon-long-arrow-right"></i></a>
        </div><!-- End .more-container -->
    </div>
    @if (home_setting('signup_offer_enabled'))
        <div class="cta cta-display bg-image pt-4 pb-4"
            style="background-image: url('{{ home_asset('signup_offer_background') }}');">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9 col-xl-8">
                        <div class="row no-gutters flex-column flex-sm-row align-items-sm-center">
                            <div class="col">
                                <h3 class="cta-title text-white">{{ home_setting('signup_offer_title') }}</h3><!-- End .cta-title -->
                                @if (home_setting('signup_offer_text'))
                                    <p class="cta-desc text-white">{{ home_setting('signup_offer_text') }}</p>
                                @endif
                            </div><!-- End .col -->

                            @if (home_setting('signup_offer_button_text'))
                                <div class="col-auto">
                                    <a href="{{ home_link(home_setting('signup_offer_button_link')) }}" class="btn btn-outline-white"><span>{{ home_setting('signup_offer_button_text') }}</span><i
                                            class="icon-long-arrow-right"></i></a>
                                </div><!-- End .col-auto -->
                            @endif
                        </div><!-- End .row no-gutters -->
                    </div><!-- End .col-md-10 col-lg-9 -->
                </div><!-- End .row -->
            </div><!-- End .container -->
        </div>
    @endif
    <!-- End .cta -->
@endsection

@section('script')
    @include('partials.home.product_actions_script')
@endsection
