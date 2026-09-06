@extends('admin.layouts.app')

@section('title', 'Pages')
@section('subTitle', 'Page List')

@section('style')
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Page Settings</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Edit the title, label, description and content of every static storefront page from one place.
                        The front-end fetches each page by its <strong>slug</strong>. Use the switch to show / hide a page,
                        or the trash button to soft-delete it.
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">All Pages</h3>
                </div>

                <div class="card-body">
                    @include('partials._message')

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="pageTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Page Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
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

@section('script')
    <script>
        $(function () {

            var pageTable = $('#pageTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/admin/pages',
                columns: [
                    { data: 'id',         name: 'id' },
                    { data: 'name',       name: 'name' },
                    { data: 'slug',       name: 'slug' },
                    { data: 'status',     name: 'status', orderable: false, searchable: false },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action',     name: 'action', orderable: false, searchable: false },
                ],
                order: [[0, 'asc']],
            });

            // Soft delete (the generic .toggle-status handler in admin.js covers the status switch)
            $(document).on('click', '.delete-page', function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This page will be hidden from the storefront (soft delete).',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/admin/delete_page/' + id,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                pageTable.ajax.reload(null, false);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                            }
                        },
                        error: function (xhr) {
                            var res = xhr.responseJSON;
                            Swal.fire({ icon: 'error', title: 'Error!', text: (res && res.message) || 'Something went wrong' });
                        },
                    });
                });
            });

        });
    </script>
@endsection
