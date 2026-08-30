@extends('layouts.insight')
@section('title', 'About Us')
@section('subTitle', 'About Us')

@section('style')
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

                            <a href="{{ route('blog') }}" class="btn btn-sm btn-minwidth btn-outline-primary-2">
                                <span>VIEW OUR NEWS</span>
                                <i class="icon-long-arrow-right"></i>
                            </a>
                        </div><!-- End .col-lg-5 -->

                        <div class="col-lg-6 offset-lg-1">
                            <div class="about-images">
                                <img src="{{ asset('assets/images/about/img-1.jpg') }}" alt="SelfBuy" class="about-img-front">
                                <img src="{{ asset('assets/images/about/img-2.jpg') }}" alt="SelfBuy" class="about-img-back">
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

                {{-- Static for now — team member cards will move to the admin panel
                    once that management screen is built; this grid is already
                    laid out for however many people that ends up being. --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="member member-anim text-center">
                            <figure class="member-media">
                                <img src="{{ asset('assets/images/team/member-3.jpg') }}" alt="Vivek Singh">

                                <figcaption class="member-overlay">
                                    <div class="member-overlay-content">
                                        <h3 class="member-title">Vivek Singh<span>Founder & Owner</span></h3><!-- End .member-title -->
                                        <p>Built SelfBuy from the ground up in 2026 — from the product catalog to checkout to customer support.</p>
                                        <div class="social-icons social-icons-simple">
                                            <a href="mailto:{{ config('mail.from.address') }}" class="social-icon" title="Email"><i class="icon-envelope"></i></a>
                                        </div><!-- End .soial-icons -->
                                    </div><!-- End .member-overlay-content -->
                                </figcaption><!-- End .member-overlay -->
                            </figure><!-- End .member-media -->
                            <div class="member-content">
                                <h3 class="member-title">Vivek Singh<span>Founder & Owner</span></h3><!-- End .member-title -->
                            </div><!-- End .member-content -->
                        </div><!-- End .member -->
                    </div><!-- End .col-md-4 -->

                    <div class="col-md-4">
                        <div class="member member-anim text-center">
                            <figure class="member-media">
                                <img src="{{ asset('assets/images/team/member-1.jpg') }}" alt="Team member">

                                <figcaption class="member-overlay">
                                    <div class="member-overlay-content">
                                        <h3 class="member-title">Team Member<span>Customer Support</span></h3><!-- End .member-title -->
                                        <p>Here to help with orders, returns and anything else you need.</p>
                                        <div class="social-icons social-icons-simple">
                                            <a href="mailto:{{ config('mail.from.address') }}" class="social-icon" title="Email"><i class="icon-envelope"></i></a>
                                        </div><!-- End .soial-icons -->
                                    </div><!-- End .member-overlay-content -->
                                </figcaption><!-- End .member-overlay -->
                            </figure><!-- End .member-media -->
                            <div class="member-content">
                                <h3 class="member-title">Team Member<span>Customer Support</span></h3><!-- End .member-title -->
                            </div><!-- End .member-content -->
                        </div><!-- End .member -->
                    </div><!-- End .col-md-4 -->

                    <div class="col-md-4">
                        <div class="member member-anim text-center">
                            <figure class="member-media">
                                <img src="{{ asset('assets/images/team/member-2.jpg') }}" alt="Team member">

                                <figcaption class="member-overlay">
                                    <div class="member-overlay-content">
                                        <h3 class="member-title">Team Member<span>Operations</span></h3><!-- End .member-title -->
                                        <p>Keeps the catalog, orders and deliveries running smoothly.</p>
                                        <div class="social-icons social-icons-simple">
                                            <a href="mailto:{{ config('mail.from.address') }}" class="social-icon" title="Email"><i class="icon-envelope"></i></a>
                                        </div><!-- End .soial-icons -->
                                    </div><!-- End .member-overlay-content -->
                                </figcaption><!-- End .member-overlay -->
                            </figure><!-- End .member-media -->
                            <div class="member-content">
                                <h3 class="member-title">Team Member<span>Operations</span></h3><!-- End .member-title -->
                            </div><!-- End .member-content -->
                        </div><!-- End .member -->
                    </div><!-- End .col-md-4 -->
                </div><!-- End .row -->
            </div><!-- End .container -->
        </div><!-- End .page-content -->
    </main>
@endsection

@section('script')
@endsection
