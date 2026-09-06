@extends('admin.layouts.app')

@section('title', 'Add Team Member')
@section('subTitle', 'Team Members')

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs b-none">
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.create_team_member') }}">Add Member</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.team_members') }}"><i class="fa fa-list-ul"></i> Team List</a></li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Add Team Member</h3></div>
                <div class="card-body">
                    @include('partials._message')
                    @include('admin.team._form', [
                        'member'      => null,
                        'formId'      => 'teamMemberForm',
                        'submitLabel' => 'Add Member',
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
