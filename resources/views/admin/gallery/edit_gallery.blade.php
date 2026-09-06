@extends('admin.layouts.app')

@section('title', 'Edit Gallery Image')
@section('subTitle', 'Gallery')

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs b-none">
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.edit_gallery', $item->id) }}">Edit Image</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.gallery') }}"><i class="fa fa-th"></i> All Images</a></li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Edit Gallery Image #{{ $item->id }}</h3></div>
                <div class="card-body">
                    @include('partials._message')
                    @include('admin.gallery._form', [
                        'item'        => $item,
                        'formId'      => 'editGalleryForm',
                        'submitLabel' => 'Update Image',
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
