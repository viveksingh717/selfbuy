@extends('errors.layout')

{{-- The error itself is logged / sent to Bugsnag - visitors only see this. --}}
@section('title', 'Something Went Wrong')
@section('code', '500')
@section('heading', 'Something went wrong')
@section('message', 'An unexpected error occurred on our side. Our team has been notified. Please try again in a moment.')
@section('actions')
    <a class="btn btn-primary" href="javascript:location.reload()">Try Again</a>
    <a class="btn btn-outline" href="{{ url('/') }}">Go to Home</a>
@endsection
