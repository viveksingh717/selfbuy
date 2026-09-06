@extends('admin.layouts.app')

@section('title', 'Add Gallery Image')
@section('subTitle', 'Gallery')

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs b-none">
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.create_gallery') }}">Add Image</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.gallery') }}"><i class="fa fa-th"></i> All Images</a></li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Add Gallery Image</h3></div>
                <div class="card-body">
                    @include('partials._message')
                    @include('admin.gallery._form', [
                        'item'        => null,
                        'formId'      => 'galleryForm',
                        'submitLabel' => 'Add Image',
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
