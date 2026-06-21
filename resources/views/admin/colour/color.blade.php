@extends('admin.layouts.app')

@section('title', 'Color')
@section('subTitle', 'Color List')

@section('style')
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="card">
                <div class="card-body">

                    <div class="d-md-flex justify-content-between mb-3">
                        <ul class="nav nav-tabs b-none">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.color') }}">
                                    <i class="fa fa-list-ul"></i> Color List
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.create_color') }}">
                                    <i class="fa fa-plus"></i> Add New
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

            {{-- TABLE --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Color List</h3>
                </div>

                <div class="card-body">
                    @include('partials._message')
                    <div class="table-responsive">

                        <table class="table table-hover table-striped" id="colorTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Color Name</th>
                                    <th>Color Code</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- Data loaded via DataTables AJAX --}}
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

        </div>
    </div>

@endsection
