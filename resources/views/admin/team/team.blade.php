@extends('admin.layouts.app')

@section('title', 'Team Members')
@section('subTitle', 'Team Members')

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex justify-content-between">
                        <ul class="nav nav-tabs b-none">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.team_members') }}">
                                    <i class="fa fa-list-ul"></i> Team List
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.create_team_member') }}">
                                    <i class="fa fa-plus"></i> Add Member
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Team Members</h3>
                    <div class="card-options text-muted small">Shown in the <strong>Meet Our Team</strong> section of the About&nbsp;Us page</div>
                </div>
                <div class="card-body">
                    @include('partials._message')

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="teamTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
            var table = $('#teamTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/admin/team_members',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'photo', name: 'photo', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'designation', name: 'designation' },
                    { data: 'sort_order', name: 'sort_order' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[4, 'asc']],
                lengthChange: false,
            });

            $(document).on('click', '.delete-team-member', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This team member will be removed from the About Us page.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete it!',
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '/admin/delete_team_member/' + id,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (res) {
                            Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1400, showConfirmButton: false });
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({ icon: 'error', title: 'Error!', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong' });
                        },
                    });
                });
            });
        });
    </script>
@endsection
