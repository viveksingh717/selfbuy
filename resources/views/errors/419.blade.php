@extends('errors.layout')

{{-- CSRF token expired - usually a form left open too long. --}}
@section('title', 'Page Expired')
@section('code', '419')
@section('heading', 'Your session has expired')
@section('message', 'For your security, this page expired after being open for a while. Please go back, refresh the page and try again.')
@section('actions')
    <a class="btn btn-primary" href="javascript:history.back()">Go Back &amp; Retry</a>
    <a class="btn btn-outline" href="{{ url('/') }}">Go to Home</a>
@endsection
