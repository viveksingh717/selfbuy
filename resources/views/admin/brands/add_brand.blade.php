@extends('admin.layouts.app')
@section('title', 'Add Brand')
@section('subTitle', 'Brand')

@section('style')
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-md-flex justify-content-between mb-2">
                                <ul class="nav nav-tabs b-none">
                                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.create_brand') }}">
                                            Add Brand</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.brand') }}"><i
                                                class="fa fa-list-ul"></i>Brand List</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add Brand</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="brandForm" enctype="multipart/form-data" class="card-body" id="brandForm">
                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Brand Name</label>
                                            <input type="text" class="form-control" name="brand_name" id="brand_name"
                                                placeholder="Enter Brand Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Brand Slug</label>
                                            <input type="text" class="form-control" name="brand_slug" id="brand_slug"
                                                readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <label>Status</label>
                                        <select class="form-control show-tick" name="status" id="status">
                                            <option value="">-- Status --</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Brand Image</label>

                                            <div id="brandDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop image here or click to upload
                                                </div>
                                            </div>

                                            <input type="hidden" name="brand_image" id="brand_image">

                                            <small class="form-text text-muted">
                                                Upload brand image
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" name="brand_submit"
                                            id="brand_form_submit">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary" name="cancel"
                                            id="brand_form_cancel">Cancel</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')

    <script></script>

@endsection
