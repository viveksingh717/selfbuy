@extends('admin.layouts.app')
@section('title', 'Add SubCategory')
@section('subTitle', 'SubCategory')

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
                                    <li class="nav-item"><a class="nav-link active"
                                            href="{{ route('admin.create_subcategory') }}"> Add SubCategory</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.sub_category') }}"><i
                                                class="fa fa-list-ul"></i>SubCategory List</a></li>
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
                            <h3 class="card-title">Add SubCategory</h3>
                            {{-- <div class="card-options ">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fa fa-times"></i></a>
                        </div> --}}
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="subCategoryForm" class="card-body" id="subCategoryForm">
                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>SubCategory Name</label>
                                            <input type="text" class="form-control" name="subcategory_name"
                                                id="subcategory_name" placeholder="Enter SubCategory Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>SubCategory Slug</label>
                                            <input type="text" class="form-control" name="subcategory_slug"
                                                id="subcategory_slug" readonly>
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
                                    <div class="col-md-6 col-sm-12">
                                        <label>Parent Category</label>
                                        <select class="form-control show-tick" name="category_id" id="category_id">
                                            <option value="">-- Select Category --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>SubCategory Image</label>

                                            <div id="subcategoryDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop image here or click to upload
                                                </div>
                                            </div>

                                            <input type="hidden" name="subcategory_image" id="subcategory_image">

                                            <small class="form-text text-muted">
                                                Upload subcategory image
                                            </small>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" name="subcategory_submit"
                                            id="subcategory_form_submit">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary" name="cancel"
                                            id="subcategory_form_cancel">Cancel</button>
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

    <script>
        $(document).ready(function() {

            // $('#cat_description').summernote({
            //     placeholder: 'Description for the category'
            // });

            // $('#meta_description').summernote({
            //     placeholder: 'Meta description for the category'
            // });
        });
    </script>

@endsection
