@extends('layouts.insight')
@section('title', 'About Us')
@section('subTitle', 'About Us')

@section('style')
    <style>
        /* Team photos are uploaded in any size - show them all in the same 3:4 frame. */
        .member-media { aspect-ratio: 3 / 4; overflow: hidden; }
        .member-media img { width: 100%; height: 100%; object-fit: cover; object-position: center top; }
    </style>
@endsection

@section('content')
    <main class="main">
        <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About us</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->
        <div class="container">
            <div class="page-header page-header-big text-center" style="background-image: url('{{ asset('assets/images/about-header-bg.jpg') }}')">
                <h1 class="page-title text-white">About Us</h1>
            </div><!-- End .page-header -->
        </div><!-- End .container -->

        <div class="page-content pb-0">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <h2 class="title">Our Vision</h2><!-- End .title -->
                        <p>To be the online shopping destination people trust by default — where finding a genuine product at a fair price, checking out securely, and getting real answers from other buyers all happen in one place, without the guesswork that usually comes with shopping online.</p>
                    </div><!-- End .col-lg-6 -->

                    <div class="col-lg-6">
                        <h2 class="title">Our Mission</h2><!-- End .title -->
                        <p>We curate quality products across categories, keep pricing honest, and back every order with secure checkout and dependable delivery. <br>Every feature on SelfBuy — from verified buyer reviews to multiple ways to pay — exists to make one thing simple: shopping with confidence.</p>
                    </div><!-- End .col-lg-6 -->
                </div><!-- End .row -->

                <div class="mb-5"></div><!-- End .mb-4 -->
            </div><!-- End .container -->

            <div class="bg-light-2 pt-6 pb-5 mb-6 mb-lg-8">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 mb-3 mb-lg-0">
                            <h2 class="title">Who We Are</h2><!-- End .title -->
                            <p class="lead text-primary mb-3">Founded in 2026 by Vivek Singh</p><!-- End .lead text-primary -->
                            <p class="mb-2">SelfBuy started with a simple idea: online shopping shouldn't feel like a gamble. Built from the ground up as a one-stop destination for quality products and a genuinely seamless experience, SelfBuy is designed and run independently — every part of it, from the catalog to checkout to customer support, built with the same goal in mind: shop smart, buy better.</p>

                            {{-- Hidden until the blog has real posts - the /blog page still shows template content.
                            <a href="{{ route('blog') }}" class="btn btn-sm btn-minwidth btn-outline-primary-2">
                                <span>VIEW OUR NEWS</span>
                                <i class="icon-long-arrow-right"></i>
                            </a>
                            --}}
                        </div><!-- End .col-lg-5 -->

                        <div class="col-lg-6 offset-lg-1">
                            {{-- Managed from Admin > Home Settings > About Page. --}}
                            <div class="about-images">
                                <img src="{{ home_asset('about_image_front') }}" alt="SelfBuy" class="about-img-front">
                                <img src="{{ home_asset('about_image_back') }}" alt="SelfBuy" class="about-img-back">
                            </div><!-- End .about-images -->
                        </div><!-- End .col-lg-6 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .bg-light-2 pt-6 pb-6 -->

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-sm-6">
                        <div class="icon-box icon-box-card text-center">
                            <span class="icon-box-icon">
                                <i class="icon-mobile"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Wide Selection</h3><!-- End .icon-box-title -->
                                <p>Quality products across categories, all in one catalog</p>
                            </div><!-- End .icon-box-content -->
                        </div><!-- End .icon-box -->
                    </div><!-- End .col-lg-3 col-sm-6 -->

                    <div class="col-lg-3 col-sm-6">
                        <div class="icon-box icon-box-card text-center">
                            <span class="icon-box-icon">
                                <i class="icon-check-circle-o"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Secure Payments</h3><!-- End .icon-box-title -->
                                <p>Pay your way — cards, UPI, netbanking, PayPal & more</p>
                            </div><!-- End .icon-box-content -->
                        </div><!-- End .icon-box -->
                    </div><!-- End .col-lg-3 col-sm-6 -->

                    <div class="col-lg-3 col-sm-6">
                        <div class="icon-box icon-box-card text-center">
                            <span class="icon-box-icon">
                                <i class="icon-star"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Verified Reviews</h3><!-- End .icon-box-title -->
                                <p>Real feedback, only from customers who actually bought it</p>
                            </div><!-- End .icon-box-content -->
                        </div><!-- End .icon-box -->
                    </div><!-- End .col-lg-3 col-sm-6 -->

                    <div class="col-lg-3 col-sm-6">
                        <div class="icon-box icon-box-card text-center">
                            <span class="icon-box-icon">
                                <i class="icon-truck"></i>
                            </span>
                            <div class="icon-box-content">
                                <h3 class="icon-box-title">Reliable Delivery</h3><!-- End .icon-box-title -->
                                <p>Orders tracked from checkout to your doorstep</p>
                            </div><!-- End .icon-box-content -->
                        </div><!-- End .icon-box -->
                    </div><!-- End .col-lg-3 col-sm-6 -->
                </div><!-- End .row -->

                <hr class="mt-4 mb-6">

                <h2 class="title text-center mb-4">Meet Our Team</h2><!-- End .title text-center mb-2 -->

                {{-- Managed from Admin > Team Members (App\Models\TeamMember).
                    $teamMembers is injected by a view composer in AppServiceProvider. --}}
                <div class="row">
                    @forelse ($teamMembers ?? [] as $member)
                        <div class="col-md-4">
                            <div class="member member-anim text-center">
                                <figure class="member-media">
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}">

                                    <figcaption class="member-overlay">
                                        <div class="member-overlay-content">
                                            <h3 class="member-title">{{ $member->name }}<span>{{ $member->designation }}</span></h3><!-- End .member-title -->
                                            @if ($member->bio)<p>{{ $member->bio }}</p>@endif
                                            <div class="social-icons social-icons-simple">
                                                @if ($member->facebook_url)
                                                    <a href="{{ $member->facebook_url }}" class="social-icon" title="Facebook" target="_blank" rel="noopener"><i class="icon-facebook-f"></i></a>
                                                @endif
                                                @if ($member->twitter_url)
                                                    <a href="{{ $member->twitter_url }}" class="social-icon" title="Twitter" target="_blank" rel="noopener"><i class="icon-twitter"></i></a>
                                                @endif
                                                @if ($member->instagram_url)
                                                    <a href="{{ $member->instagram_url }}" class="social-icon" title="Instagram" target="_blank" rel="noopener"><i class="icon-instagram"></i></a>
                                                @endif
                                                @if ($member->linkedin_url)
                                                    <a href="{{ $member->linkedin_url }}" class="social-icon" title="LinkedIn" target="_blank" rel="noopener"><i class="icon-linkedin"></i></a>
                                                @endif
                                                @if ($member->email)
                                                    <a href="mailto:{{ $member->email }}" class="social-icon" title="Email"><i class="icon-envelope"></i></a>
                                                @endif
                                            </div><!-- End .soial-icons -->
                                        </div><!-- End .member-overlay-content -->
                                    </figcaption><!-- End .member-overlay -->
                                </figure><!-- End .member-media -->
                                <div class="member-content">
                                    <h3 class="member-title">{{ $member->name }}<span>{{ $member->designation }}</span></h3><!-- End .member-title -->
                                </div><!-- End .member-content -->
                            </div><!-- End .member -->
                        </div><!-- End .col-md-4 -->
                    @empty
                        <div class="col-12 text-center text-muted">Our team details are coming soon.</div>
                    @endforelse
                </div><!-- End .row -->
            </div><!-- End .container -->
        </div><!-- End .page-content -->
    </main>
@endsection

@section('script')
@endsection
