@extends('admin.layouts.app')

@section('title', 'Gallery')
@section('subTitle', 'Gallery')

@section('style')
    <style>
        .gallery-card .img-wrap { position: relative; display: block; }
        .gallery-card .img-wrap img { width: 100%; height: 200px; object-fit: cover; }
        .gallery-card .status-dot { position: absolute; top: 8px; right: 8px; }
        .gallery-card.is-inactive { opacity: .55; }
        .gallery-actions { gap: 14px; }
        .gallery-actions .custom-switch { margin-right: auto !important; }
    </style>
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @include('partials._message')

                            <form method="GET" action="{{ route('admin.gallery') }}" class="page-options d-flex align-items-center">
                                <div class="input-icon ml-2">
                                    <span class="input-icon-addon"><i class="fa fa-search"></i></span>
                                    <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Search photo">
                                </div>

                                @if ($albums->isNotEmpty())
                                    <select name="album" class="form-control ml-2 w-auto" onchange="this.form.submit()">
                                        <option value="">All albums</option>
                                        @foreach ($albums as $a)
                                            <option value="{{ $a }}" {{ $album === $a ? 'selected' : '' }}>{{ $a }}</option>
                                        @endforeach
                                    </select>
                                @endif

                                <button type="submit" class="btn btn-secondary ml-2">Search</button>
                                <a href="{{ route('admin.create_gallery') }}" class="btn btn-primary ml-2">Upload New</a>
                            </form>

                            <div class="page-subtitle ml-0">
                                @if ($galleries->total())
                                    {{ $galleries->firstItem() }} - {{ $galleries->lastItem() }} of {{ $galleries->total() }} photos
                                @else
                                    No photos yet
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                @forelse ($galleries as $g)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card p-3 gallery-card {{ $g->status ? '' : 'is-inactive' }}" id="gallery-card-{{ $g->id }}">
                            <a href="{{ route('admin.edit_gallery', $g->id) }}" class="img-wrap mb-3">
                                <img src="{{ $g->image_url }}" alt="{{ $g->title }}" class="rounded">
                                <span class="status-dot badge badge-{{ $g->status ? 'success' : 'secondary' }}">
                                    {{ $g->status ? 'Active' : 'Inactive' }}
                                </span>
                            </a>

                            <div class="d-flex align-items-center px-2">
                                <div class="mr-3">
                                    <span class="avatar avatar-md" style="background:#eef2ff;color:#4e73df;">
                                        <i class="fa fa-picture-o"></i>
                                    </span>
                                </div>
                                <div class="text-truncate">
                                    <div class="text-truncate">{{ $g->title ?: 'Untitled' }}</div>
                                    <small class="d-block text-muted text-truncate">
                                        @if ($g->album)<span class="badge badge-info mr-1">{{ $g->album }}</span>@endif
                                        {{ $g->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>

                            <div class="gallery-actions d-flex align-items-center justify-content-end px-2 pt-2 mt-2 border-top">
                                <label class="custom-switch m-0" title="Toggle status">
                                    <input type="checkbox" class="custom-switch-input toggle-status"
                                        data-id="{{ $g->id }}" data-url="/admin/gallery_status"
                                        {{ $g->status ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                </label>
                                <a href="{{ route('admin.edit_gallery', $g->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-gallery"
                                    data-id="{{ $g->id }}" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card"><div class="card-body text-center text-muted py-5">
                            No photos found.
                            <a href="{{ route('admin.create_gallery') }}">Upload the first one</a>.
                        </div></div>
                    </div>
                @endforelse
            </div>

            @if ($galleries->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $galleries->links('pagination::bootstrap-4') }}
                </div>
            @endif

        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function () {
            $(document).on('click', '.delete-gallery', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Delete this image?',
                    text: 'It will be removed from the gallery (soft delete).',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete it!',
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/admin/delete_gallery/' + id,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (res) {
                            $('#gallery-card-' + id).closest('.col-sm-6').remove();
                            Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1400, showConfirmButton: false });
                        },
                        error: function (xhr) {
                            Swal.fire({ icon: 'error', title: 'Error!', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong' });
                        },
                    });
                });
            });

            // reflect active/inactive dimming when the switch is flipped
            $(document).on('change', '.gallery-card .toggle-status', function () {
                $(this).closest('.gallery-card').toggleClass('is-inactive', !this.checked);
                var $dot = $(this).closest('.gallery-card').find('.status-dot');
                $dot.toggleClass('badge-success', this.checked).toggleClass('badge-secondary', !this.checked)
                    .text(this.checked ? 'Active' : 'Inactive');
            });
        });
    </script>
@endsection
