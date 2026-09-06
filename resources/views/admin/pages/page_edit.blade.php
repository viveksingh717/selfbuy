@extends('admin.layouts.app')

@section('title', 'Edit Page')
@section('subTitle', $page->name ?: $page->slug)

@section('style')
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex justify-content-between mb-2">
                        <ul class="nav nav-tabs b-none">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.edit_page', $page->id) }}">
                                    Edit &ldquo;{{ $page->name ?: $page->slug }}&rdquo;
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.pages') }}">
                                    <i class="fa fa-list-ul"></i> Page List
                                </a>
                            </li>
                        </ul>
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
                            <h3 class="card-title">Page Settings</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')

                            <form name="pageForm" id="pageForm" class="card-body" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{ $page->id }}">

                                {{-- ── Basic ─────────────────────────────────────── --}}
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Page Name</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ old('name', $page->name) }}" placeholder="e.g. About Us">
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Slug <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                value="{{ old('slug', $page->slug) }}" placeholder="about-us">
                                            <small class="text-muted">The front-end fetches this page by its slug.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select class="form-control show-tick" name="status" id="status">
                                                <option value="1" {{ old('status', $page->status) == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $page->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Menu / Link Label</label>
                                            <input type="text" class="form-control" name="label" id="label"
                                                value="{{ old('label', $page->label) }}" placeholder="e.g. About Us">
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-sm-12">
                                        <div class="form-group">
                                            <label>Page Title / Heading</label>
                                            <input type="text" class="form-control" name="title" id="title"
                                                value="{{ old('title', $page->title) }}" placeholder="Heading shown on the page">
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Descriptions ──────────────────────────────── --}}
                                <div class="row clearfix">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control summernote" name="short_description" id="short_description">{{ old('short_description', $page->short_description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea class="form-control summernote" name="description" id="description">{{ old('description', $page->description) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Content <small class="text-muted">(optional extra block)</small></label>
                                            <textarea class="form-control summernote" name="content" id="content">{{ old('content', $page->content) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── File Attachment (optional) ────────────────── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    File Attachment <span class="text-lowercase font-weight-normal">(optional)</span>
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Upload File</label>
                                            <input type="file" class="form-control-file" name="file" id="file"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp">
                                            <small class="text-muted d-block mt-1">
                                                PDF / DOC / XLS / image, max 10 MB. Uploading replaces the current file.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label>Current File</label>
                                        <div class="form-group">
                                            @if ($page->file_path)
                                                <p class="mb-1">
                                                    <i class="fa fa-paperclip"></i>
                                                    <a href="{{ $page->file_url }}" target="_blank" rel="noopener">
                                                        {{ basename($page->file_path) }}
                                                    </a>
                                                </p>
                                                <label class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="remove_file" value="1">
                                                    <span class="form-check-label text-danger">Remove current file</span>
                                                </label>
                                            @else
                                                <p class="text-muted mb-0">No file uploaded.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── SEO / Meta ────────────────────────────────── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    SEO / Meta
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="meta_title"
                                                value="{{ old('meta_title', $page->meta_title) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_keywords" id="meta_keywords"
                                                value="{{ old('meta_keywords', $page->meta_keywords) }}"
                                                placeholder="comma, separated, keywords">
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea class="form-control" name="meta_description" id="meta_description" rows="2">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" id="page_form_submit">Update</button>
                                        <a href="{{ route('admin.pages') }}" class="btn btn-outline-secondary">Cancel</a>
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
    {{-- Behaviour for this form lives in the Pages module of
         resources/js/module/admin.js (bundled via Vite). --}}
@endsection
