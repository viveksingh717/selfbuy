@extends('errors.layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('heading', 'Slow down a little')
@section('message', 'We received too many requests from you in a short time. Please wait a moment and try again.')
