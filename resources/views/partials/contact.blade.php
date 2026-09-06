@extends('layouts.insight')
@section('title', 'Contact Us')
@section('subTitle', 'Contact Us')

@section('style')
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .contact-form button[type="submit"][disabled] { opacity: .7; cursor: not-allowed; }
    </style>
@endsection

@section('content')
    <main class="main">
        <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact us</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->
        <div class="container">
            <div class="page-header page-header-big text-center" style="background-image: url('{{ asset('assets/images/contact-header-bg.jpg') }}')">
                <h1 class="page-title text-white">Contact us<span class="text-white">keep in touch with us</span></h1>
            </div><!-- End .page-header -->
        </div><!-- End .container -->

        <div class="page-content pb-0">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-2 mb-lg-0">
                        <h2 class="title mb-1">Contact Information</h2><!-- End .title mb-2 -->
                        <p class="mb-3">Have a question about an order, a product, or anything else? Reach us directly using the details below, or send a message with the form.</p>
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="contact-info">
                                    <h3>SelfBuy</h3>

                                    <ul class="contact-list">
                                        <li>
                                            <i class="icon-map-marker"></i>
                                            New Jay Om Shanti CHS, P. K. Road, Mira Bhayandar Road, Mira Road (East), Thane – 401107, Maharashtra, India
                                        </li>
                                        <li>
                                            <i class="icon-phone"></i>
                                            <a href="tel:+919004069694">+91 90040 69694</a> / <a href="tel:+918689961600">+91 86899 61600</a>
                                        </li>
                                        <li>
                                            <i class="icon-envelope"></i>
                                            <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
                                        </li>
                                    </ul><!-- End .contact-list -->
                                </div><!-- End .contact-info -->
                            </div><!-- End .col-sm-7 -->

                            <div class="col-sm-5">
                                <div class="contact-info">
                                    <h3>Support</h3>

                                    <ul class="contact-list">
                                        <li>
                                            <i class="icon-clock-o"></i>
                                            <span class="text-dark">We aim to reply</span> <br>within 24 hours
                                        </li>
                                    </ul><!-- End .contact-list -->
                                </div><!-- End .contact-info -->
                            </div><!-- End .col-sm-5 -->
                        </div><!-- End .row -->
                    </div><!-- End .col-lg-6 -->
                    <div class="col-lg-6">
                        <span id="contact-form"></span>
                        <h2 class="title mb-1">Got Any Questions?</h2><!-- End .title mb-2 -->
                        <p class="mb-2">Use the form below to get in touch with the sales team</p>

                        @if (session('contact_success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('contact_success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST" class="contact-form mb-3">
                            @csrf

                            {{-- Honeypot: hidden from real users, catches bots --}}
                            <input type="text" name="website" tabindex="-1" autocomplete="off"
                                style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">

                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="cname" class="sr-only">Name</label>
                                    <input type="text" name="name" class="form-control" id="cname"
                                        value="{{ old('name') }}" placeholder="Name *" required>
                                </div><!-- End .col-sm-6 -->

                                <div class="col-sm-6">
                                    <label for="cemail" class="sr-only">Email</label>
                                    <input type="email" name="email" class="form-control" id="cemail"
                                        value="{{ old('email') }}" placeholder="Email *" required>
                                </div><!-- End .col-sm-6 -->
                            </div><!-- End .row -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="cphone" class="sr-only">Phone</label>
                                    <input type="tel" name="phone" class="form-control" id="cphone"
                                        value="{{ old('phone') }}" placeholder="Phone">
                                </div><!-- End .col-sm-6 -->

                                <div class="col-sm-6">
                                    <label for="csubject" class="sr-only">Subject</label>
                                    <input type="text" name="subject" class="form-control" id="csubject"
                                        value="{{ old('subject') }}" placeholder="Subject">
                                </div><!-- End .col-sm-6 -->
                            </div><!-- End .row -->

                            <label for="cmessage" class="sr-only">Message</label>
                            <textarea class="form-control" name="message" cols="30" rows="4" id="cmessage"
                                required placeholder="Message *">{{ old('message') }}</textarea>

                            <button type="submit" class="btn btn-outline-primary-2 btn-minwidth-sm">
                                <span>SUBMIT</span>
                                <i class="icon-long-arrow-right"></i>
                            </button>
                        </form><!-- End .contact-form -->
                    </div><!-- End .col-lg-6 -->
                </div><!-- End .row -->

                <hr class="mt-4 mb-5">

                @php
                    $fullAddress = 'New Jay Om Shanti CHS, P. K. Road, Mira Bhayandar Road, Mira Road East, Thane 401107, Maharashtra, India';
                    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($fullAddress);
                @endphp
                <div class="stores mb-4 mb-lg-5">
                    <h2 class="title text-center mb-3">Our Location</h2><!-- End .title text-center mb-2 -->

                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="store">
                                <div class="store-content text-center">
                                    <h3 class="store-title">SelfBuy</h3><!-- End .store-title -->
                                    <address>{{ $fullAddress }}</address>
                                    <div><a href="tel:+919004069694">+91 90040 69694</a> / <a href="tel:+918689961600">+91 86899 61600</a></div>

                                    <a href="{{ $mapsUrl }}" class="btn btn-link" target="_blank" rel="noopener"><span>View Map</span><i class="icon-long-arrow-right"></i></a>
                                </div><!-- End .store-content -->
                            </div><!-- End .store -->
                        </div><!-- End .col-lg-8 -->
                    </div><!-- End .row -->
                </div><!-- End .stores -->
            </div><!-- End .container -->
            {{-- <div id="map"></div><!-- End #map --> --}}
        </div><!-- End .page-content -->
    </main>
@endsection

@section('script')
    <script>
        (function () {
            var form = document.querySelector('.contact-form');
            if (!form) return;

            form.addEventListener('submit', function () {
                var btn = form.querySelector('button[type="submit"]');
                if (!btn || btn.dataset.submitting === '1') return;

                btn.dataset.submitting = '1';
                btn.disabled = true;
                btn.innerHTML = '<span>PLEASE WAIT&hellip;</span> <i class="icon-refresh" style="display:inline-block;animation:spin 0.8s linear infinite;"></i>';
            });
        })();
    </script>
@endsection
