@extends('errors.layout')

@section('title', 'Page Not Found')
@section('code', '404')
@section('heading', 'Page not found')
@section('message', 'The page you are looking for doesn\'t exist or has been moved.')
@section('actions')
    <a class="btn btn-primary" href="{{ url('/') }}">Go to Home</a>
    <a class="btn btn-outline" href="{{ url('/products') }}">Browse Products</a>
@endsection
