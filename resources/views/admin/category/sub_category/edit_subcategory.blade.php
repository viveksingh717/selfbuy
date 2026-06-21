@extends('admin.layouts.app')
@section('title', 'Update SubCategory')
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
                                            href="{{ route('admin.edit_subcategory', $subcategory->id) }}"> Edit
                                            SubCategory</a></li>
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
                            <h3 class="card-title">Update SubCategory</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="editSubCategoryForm" class="card-body" id="editSubCategoryForm">
                                <input type="hidden" name="id" value="{{ $subcategory->id }}">

                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>SubCategory Name</label>
                                            <input type="text" class="form-control" name="subcategory_name"
                                                id="subcategory_name" placeholder="Enter SubCategory Name"
                                                value="{{ $subcategory->subcategory_name }}">

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>SubCategory Slug</label>
                                            <input type="text" class="form-control" name="subcategory_slug"
                                                id="subcategory_slug" readonly value="{{ $subcategory->subcategory_slug }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-3 col-sm-12">
                                        <label>Status</label>
                                        <select class="form-control show-tick" name="status" id="status">
                                            <option value="">-- Status --</option>
                                            <option value="1" {{ $subcategory->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $subcategory->status == 0 ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <label>Parent Category</label>
                                        <select class="form-control show-tick" name="category_id" id="category_id">
                                            <option value="">-- Select Category --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $subcategory->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12 mt-3">
                                    <button type="submit" class="btn btn-primary" name="subcategory_edit_submit"
                                        id="subcategory_editform_submit">Update</button>
                                    <button type="reset" class="btn btn-outline-secondary" name="cancel"
                                        id="subcat_form_cancel">Cancel</button>
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
