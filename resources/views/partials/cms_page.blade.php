@extends('layouts.insight')
@section('title', $page->meta_title ?: $page->title)
@section('subTitle', $page->title)

{{--
    Customer-service pages (payment, shipping, returns, money-back, terms, privacy).
    Title / intro / body come from Admin > Pages; until the admin writes a body, sensible
    starter content built from System Settings is shown (partials/cms_defaults).
--}}

@php
    $servicePages = [
        'payment-method'       => ['Payment Methods', 'payment'],
        'shipping'             => ['Shipping', 'shipping'],
        'refund-policy'        => ['Returns & Refunds', 'refund_policy'],
        'money-back-guarantee' => ['Money-back Guarantee', 'money_back_guarantee'],
        'terms-and-conditions' => ['Terms & Conditions', 'terms_conditions'],
        'privacy-policy'       => ['Privacy Policy', 'privacy_policy'],
    ];
    $hasAdminBody = filled(strip_tags((string) $page->content)) || filled(strip_tags((string) $page->description));
@endphp

@section('style')
    <style>
        .cms-body h3 { font-size: 2rem; margin: 2.4rem 0 1rem; }
        .cms-body ul { list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem; }
        .cms-body ul li { margin-bottom: .6rem; }
        .cms-side .widget-list li a.active { color: #c96; font-weight: 500; }
        .cms-help { background: #f9f9f9; padding: 2rem; border-radius: 4px; }
    </style>
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url('{{ asset('assets/images/page-header-bg.jpg') }}')">
            <div class="container">
                <h1 class="page-title">{{ $page->title }}</h1>
            </div>
        </div>
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item">Customer Service</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                </ol>
            </div>
        </nav>

        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 cms-body mb-4">
                        @if (filled(strip_tags((string) $page->short_description)))
                            {{-- Admin-authored rich text (summernote) --}}
                            <div class="lead mb-3">{!! $page->short_description !!}</div>
                        @endif

                        @if ($hasAdminBody)
                            {!! $page->description !!}
                            {!! $page->content !!}
                        @else
                            @include('partials.cms_defaults', ['slug' => $page->slug])
                        @endif

                        <p class="text-muted mt-4 mb-0"><small>Last updated {{ $page->updated_at->format('d M Y') }}</small></p>
                    </div>

                    <aside class="col-lg-4 cms-side">
                        <div class="widget">
                            <h3 class="widget-title">Customer Service</h3>
                            <ul class="widget-list">
                                @foreach ($servicePages as $slug => [$label, $route])
                                    <li><a href="{{ route($route) }}" class="{{ $page->slug === $slug ? 'active' : '' }}">{{ $label }}</a></li>
                                @endforeach
                                <li><a href="{{ route('track_order') }}">Track My Order</a></li>
                                <li><a href="{{ route('faq') }}">FAQ</a></li>
                            </ul>
                        </div>

                        <div class="cms-help">
                            <h4 class="mb-1">Need help?</h4>
                            <p class="mb-1">Our team is happy to help with any question.</p>
                            @if (setting('support_email'))
                                <p class="mb-0"><i class="icon-envelope"></i> <a href="mailto:{{ setting('support_email') }}">{{ setting('support_email') }}</a></p>
                            @endif
                            @if (setting('contact_phone'))
                                <p class="mb-0"><i class="icon-phone"></i> <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></p>
                            @endif
                            @if (setting('support_hours'))
                                <p class="mb-2 text-muted"><small>{{ setting('support_hours') }}</small></p>
                            @endif
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary-2 btn-sm mt-1"><span>Contact us</span></a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
@endsection
