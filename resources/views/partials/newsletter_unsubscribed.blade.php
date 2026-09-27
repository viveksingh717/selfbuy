@extends('layouts.insight')
@section('title', 'Unsubscribed')
@section('subTitle', 'Unsubscribed')

@section('content')
    <div class="page-content pt-6 pb-6">
        <div class="container text-center">
            <h1 class="title-lg mb-2">You've been unsubscribed</h1>
            <p class="mb-3">{{ $subscriber->email }} won't receive our newsletter emails anymore.</p>
            <p class="mb-4">Changed your mind? You can subscribe again any time from the newsletter popup.</p>
            <a href="{{ route('home') }}" class="btn btn-outline-primary-2"><span>Back to Home</span><i class="icon-long-arrow-right"></i></a>
        </div>
    </div>
@endsection
