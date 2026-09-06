@extends('admin.layouts.app')

@section('title', 'My Profile')
@section('subTitle', 'My Profile')

@section('style')
    <style>
        .profile-avatar,
        .profile-avatar-fallback {
            width: 120px; height: 120px; border-radius: 50%;
            object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,.12);
        }
        .profile-avatar-fallback {
            display: flex; align-items: center; justify-content: center;
            background: #4e73df; color: #fff; font-size: 40px; font-weight: 700;
        }
        .profile-meta li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f1f4; }
        .profile-meta li:last-child { border-bottom: 0; }
        .profile-meta .k { color: #98a2b3; }
    </style>
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            @include('partials._message')

            <div class="row clearfix">

                {{-- ── Summary ─────────────────────────────────────── --}}
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            @if ($admin->avatar_url)
                                <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="profile-avatar mb-3">
                            @else
                                <div class="profile-avatar-fallback mx-auto mb-3">{{ $admin->initials }}</div>
                            @endif

                            <h5 class="mb-0">{{ $admin->name }}</h5>
                            <p class="text-muted mb-2">{{ $admin->email }}</p>
                            <span class="badge badge-primary">
                                {{ (int) $admin->role_type === 1 ? 'Administrator' : 'Admin' }}
                            </span>

                            @if ($admin->avatar_url)
                                <form method="POST" action="{{ route('admin.profile.avatar.delete') }}" class="mt-3">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i> Remove photo
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="card-body pt-0">
                            <ul class="list-unstyled profile-meta mb-0">
                                <li><span class="k">Phone</span><span>{{ $admin->phone_number ?: '—' }}</span></li>
                                <li><span class="k">Location</span><span>{{ collect([$admin->city, $admin->state, $admin->country])->filter()->join(', ') ?: '—' }}</span></li>
                                <li><span class="k">Member since</span><span>{{ optional($admin->created_at)->format('d M Y') ?: '—' }}</span></li>
                                <li><span class="k">Account ID</span><span>#{{ $admin->id }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ── Editable details ────────────────────────────── --}}
                <div class="col-lg-8 col-md-12">

                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Profile Information</h3></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $admin->name) }}">
                                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $admin->email) }}">
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                                                value="{{ old('phone_number', $admin->phone_number) }}">
                                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Profile Photo</label>
                                            <input type="file" name="avatar" accept="image/*"
                                                class="form-control-file @error('avatar') is-invalid @enderror" id="avatarInput">
                                            <small class="text-muted d-block">JPG / PNG / WEBP, max 2 MB.</small>
                                            @error('avatar') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                            <img id="avatarPreview" src="#" alt="preview"
                                                style="display:none;width:70px;height:70px;border-radius:50%;object-fit:cover;margin-top:10px;">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address Line 1</label>
                                            <input type="text" name="address_line1" class="form-control"
                                                value="{{ old('address_line1', $admin->address_line1) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address Line 2</label>
                                            <input type="text" name="address_line2" class="form-control"
                                                value="{{ old('address_line2', $admin->address_line2) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>City</label>
                                            <input type="text" name="city" class="form-control"
                                                value="{{ old('city', $admin->city) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>State</label>
                                            <input type="text" name="state" class="form-control"
                                                value="{{ old('state', $admin->state) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Postal Code</label>
                                            <input type="text" name="postal_code" class="form-control"
                                                value="{{ old('postal_code', $admin->postal_code) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <input type="text" name="country" class="form-control"
                                                value="{{ old('country', $admin->country) }}">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card" id="change-password">
                        <div class="card-header"><h3 class="card-title">Change Password</h3></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.password') }}">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Current Password <span class="text-danger">*</span></label>
                                            <input type="password" name="current_password"
                                                class="form-control @error('current_password') is-invalid @enderror">
                                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>New Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror">
                                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="text-muted">Min 8 characters, with letters and numbers.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm New Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-key"></i> Update Password
                                </button>
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
        (function () {
            var input = document.getElementById('avatarInput');
            var preview = document.getElementById('avatarPreview');
            if (input) {
                input.addEventListener('change', function () {
                    var file = this.files && this.files[0];
                    if (!file) { preview.style.display = 'none'; return; }
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'inline-block';
                });
            }

            @if (session('password_tab') || $errors->has('current_password') || $errors->has('password'))
                var pw = document.getElementById('change-password');
                if (pw) pw.scrollIntoView({ behavior: 'smooth' });
            @endif
        })();
    </script>
@endsection
