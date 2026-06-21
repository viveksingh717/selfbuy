@extends('admin.layouts.app')
@section('title', 'Add Category')
@section('subTitle', 'Category')

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
                                            href="{{ route('admin.create_category') }}"> Add Category</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.category') }}"><i
                                                class="fa fa-list-ul"></i>Category List</a></li>
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
                            <h3 class="card-title">Add Category</h3>
                            {{-- <div class="card-options ">
                            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
                            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fa fa-times"></i></a>
                        </div> --}}
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="categoryForm" enctype="multipart/form-data" class="card-body" id="categoryForm">
                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Category Name</label>
                                            <input type="text" class="form-control" name="category_name"
                                                id="category_name" placeholder="Enter Category Name">

                                            {{-- <div id="category_name_error" class="error-message"></div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Category Slug</label>
                                            <input type="text" class="form-control" name="category_slug"
                                                id="category_slug" readonly>

                                            {{-- <div id="category_slug_error" class="error-message"></div> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="meta_title"
                                                placeholder="Enter Meta Title">

                                            {{-- <div id="meta_title_error" class="error-message"></div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <label>Status</label>
                                        <select class="form-control show-tick" name="status" id="status">
                                            <option value="">-- Status --</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <label>Is Featured</label>
                                        <select class="form-control show-tick" name="is_featured" id="is_featured">
                                            <option value="">-- Featured --</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control" name="cat_description" id="cat_description" placeholder="Enter Category Description"></textarea>

                                            <div id="cat_description_error" class="error-message"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea class="form-control" name="meta_description" id="meta_description" placeholder="Enter Meta Description"></textarea>

                                            {{-- <div id="meta_description_error" class="error-message"></div> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Category Image</label>

                                            <div id="categoryDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop image here or click to upload
                                                </div>
                                            </div>

                                            <input type="hidden" name="category_image" id="category_image">

                                            <small class="form-text text-muted">
                                                Upload category image
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" name="category_submit"
                                            id="category_form_submit">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary" name="cancel"
                                            id="cat_form_cancel">Cancel</button>
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

            $('#cat_description').summernote({
                placeholder: 'Description for the category'
            });

            $('#meta_description').summernote({
                placeholder: 'Meta description for the category'
            });
        });
    </script>

@endsection
