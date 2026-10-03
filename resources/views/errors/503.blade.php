@extends('errors.layout')

{{--
    Two cases share this page:
      - planned maintenance: `php artisan down --render="errors::503"` (pre-rendered, app not booted)
      - database unavailable: rendered by the handler in bootstrap/app.php with $reason = 'database'
    Both refresh themselves, so visitors get back in as soon as the site is up again.
--}}
@php $isDatabase = ($reason ?? null) === 'database'; @endphp

@section('title', $isDatabase ? 'Temporarily Unavailable' : 'Under Maintenance')
@section('refresh', '30')
@section('code', '503')
@section('heading', $isDatabase ? 'We\'re having a temporary problem' : 'We\'ll be back shortly')
@section('message', $isDatabase
    ? 'Our store is having trouble connecting to its services right now. We\'re working on it - your cart and orders are safe.'
    : 'SelfBuy is getting an update to serve you better. We\'ll be back online as soon as possible - thank you for your patience.')
@section('actions')
    <a class="btn btn-primary" href="javascript:location.reload()">Try Again</a>
@endsection
@section('note')
    <span class="spinner" aria-hidden="true"></span>This page will refresh automatically.
@endsection
