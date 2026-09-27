<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <div class="header-dropdown">
                    <a href="#">INR</a>
                    <div class="header-menu">
                        <ul>
                            <li><a href="#">INR</a></li>
                            <li><a href="#">Usd</a></li>
                        </ul>
                    </div><!-- End .header-menu -->
                </div><!-- End .header-dropdown -->

                <div class="header-dropdown">
                    <a href="#">Eng</a>
                    <div class="header-menu">
                        <ul>
                            <li><a href="#">English</a></li>
                            <li><a href="#">Hindi</a></li>
                        </ul>
                    </div><!-- End .header-menu -->
                </div><!-- End .header-dropdown -->
            </div><!-- End .header-left -->

            <div class="header-right">
                <ul class="top-menu">
                    <li>
                        <a href="#">Links</a>
                        <ul>
                            <li><a href="tel:+919004069694"><i class="icon-phone"></i>Call: +91 90040 69694</a></li>
                            <li><a href="{{ route('about') }}"><i class="icon-info-circle"></i>About Us</a></li>
                            <li><a href="{{ route('contact') }}"><i class="icon-envelope"></i>Contact Us</a></li>
                            <li><a href="{{ route('blog') }}"><i class="icon-blog"></i>Blog</a></li>
                            @guest
                                <li><a href="#signin-modal" data-toggle="modal"><i class="icon-user"></i>Login</a></li>
                                <li><a href="#signin-modal" data-toggle="modal" data-auth-tab="register-tab"><i class="icon-user"></i>Register</a></li>
                            @else
                                <li><a href="{{ route('myaccount') }}"><i class="icon-user"></i>{{ Auth::user()->name }}</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background:none;border:0;padding:0;font:inherit;color:inherit;"><i class="icon-unlock"></i>Logout</button>
                                    </form>
                                </li>
                            @endguest
                        </ul>
                    </li>
                </ul><!-- End .top-menu -->
            </div><!-- End .header-right -->
        </div><!-- End .container -->
    </div><!-- End .header-top -->

    <div class="header-middle sticky-header">
        <div class="container">
            <div class="header-left">
                <button class="mobile-menu-toggler">
                    <span class="sr-only">Toggle mobile menu</span>
                    <i class="icon-bars"></i>
                </button>

                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('horizontal_logo.png') }}" alt="SelfBuy Logo" width="160" height="25">
                </a>

                <nav class="main-nav">
                    <ul class="menu sf-arrows">
                        <li class="megamenu-container active">
                            <a href="{{ route('home') }}" class="">Home</a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="sf-with-ul">Shop</a>

                            <div class="megamenu megamenu-md">
                                <div class="row no-gutters">
                                    <div class="col-md-12">
                                        <div class="menu-col">
                                            @if (isset($headerCategories) && $headerCategories->isNotEmpty())
                                                <div class="row">
                                                    @foreach ($headerCategories as $category)
                                                        <div class="col-md-4" style="margin-bottom:20px;">
                                                            <a class="menu-title"
                                                                href="{{ url($category->category_slug) }}">
                                                                {{ $category->category_name }}
                                                                {{-- @if ($category->is_featured)
                                                                    <span class="tip tip-new">New</span>
                                                                @endif --}}
                                                            </a>
                                                            @if ($category->subcategories->isNotEmpty())
                                                                <ul>
                                                                    @foreach ($category->subcategories as $subcategory)
                                                                        <li>
                                                                            <a
                                                                                href="{{ url($category->category_slug . '/' . $subcategory->subcategory_slug) }}">{{ $subcategory->subcategory_name }}</a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div><!-- End .row -->
                                            @else
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <p class="p-3 text-muted">No categories available.</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div><!-- End .menu-col -->
                                    </div><!-- End .col-md-12 -->
                                </div><!-- End .row -->
                            </div><!-- End .megamenu megamenu-md -->
                        </li>
                        <li class="{{ request()->routeIs('products') ? 'active' : '' }}">
                            <a href="{{ route('products') }}" class="sf-with-ul">Product</a>

                            <div class="megamenu megamenu-sm">
                                <div class="row no-gutters">
                                    <div class="{{ $headerBrands->isNotEmpty() ? 'col-md-6' : 'col-md-12' }}">
                                        <div class="menu-col">
                                            <div class="menu-title">Browse Products</div><!-- End .menu-title -->
                                            <ul>
                                                <li><a href="{{ route('products') }}">All Products</a></li>
                                                <li><a href="{{ route('products', ['sort' => 'latest']) }}">New Arrivals</a></li>
                                                <li><a href="{{ route('products', ['sort' => 'price_low']) }}">Price: Low to High</a></li>
                                                <li><a href="{{ route('products', ['sort' => 'price_high']) }}">Price: High to Low</a></li>
                                            </ul>
                                        </div><!-- End .menu-col -->
                                    </div><!-- End .col-md-6 -->

                                    @if ($headerBrands->isNotEmpty())
                                        <div class="col-md-6">
                                            <div class="menu-col">
                                                <div class="menu-title">Shop by Brand</div><!-- End .menu-title -->
                                                <ul>
                                                    @foreach ($headerBrands as $brand)
                                                        <li><a href="{{ route('products', ['brand_id' => [$brand->id]]) }}">{{ $brand->brand_name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div><!-- End .menu-col -->
                                        </div><!-- End .col-md-6 -->
                                    @endif
                                </div><!-- End .row -->
                            </div><!-- End .megamenu megamenu-sm -->
                        </li>
                    </ul><!-- End .menu -->
                </nav><!-- End .main-nav -->
            </div><!-- End .header-left -->

            <div class="header-right">
                <div class="header-search">
                    <a href="#" class="search-toggle" role="button" title="Search"><i
                            class="icon-search"></i></a>
                    <form action="{{ route('search') }}" method="get">
                        <div class="header-search-wrapper">
                            <label for="q" class="sr-only">Search</label>
                            <input type="search" class="form-control" name="q" id="q"
                                value="{{ request('q') }}" placeholder="Search products..." required>
                        </div><!-- End .header-search-wrapper -->
                    </form>
                </div><!-- End .header-search -->

                <div class="dropdown cart-dropdown">
                    <a href="{{ route('cart.index') }}" class="dropdown-toggle" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" data-display="static">
                        <i class="icon-shopping-cart"></i>
                        <span class="cart-count">{{ $headerCartCount ?? 0 }}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" id="cart-dropdown-content">
                        @include('layouts.inc.cart_dropdown')
                    </div><!-- End .dropdown-menu -->
                </div><!-- End .cart-dropdown -->
            </div><!-- End .header-right -->
        </div><!-- End .container -->
    </div><!-- End .header-middle -->
</header><!-- End .header -->
