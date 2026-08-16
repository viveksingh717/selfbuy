@extends('layouts.insight')
@section('title', 'Reset Password')

@section('content')
    <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
        <div class="container">
            <h1 class="page-title">Reset Password</h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reset Password</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="form-box">
                        <div class="form-tab">
                            <h2 class="checkout-title mb-4">Choose a New Password</h2>

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group">
                                    <label for="reset-email">Email address *</label>
                                    <input type="email" class="form-control" id="reset-email" name="email"
                                        value="{{ old('email', $email) }}" required autofocus>
                                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div><!-- End .form-group -->

                                <div class="form-group">
                                    <label for="reset-password">New Password *</label>
                                    <div class="password-field-wrapper">
                                        <input type="password" class="form-control" id="reset-password" name="password"
                                            required minlength="6">
                                        <i class="icon-eye password-toggle-icon" data-target="#reset-password" title="Show password"></i>
                                    </div>
                                    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div><!-- End .form-group -->

                                <div class="form-group">
                                    <label for="reset-password-confirmation">Confirm New Password *</label>
                                    <div class="password-field-wrapper">
                                        <input type="password" class="form-control" id="reset-password-confirmation"
                                            name="password_confirmation" required minlength="6">
                                        <i class="icon-eye password-toggle-icon" data-target="#reset-password-confirmation" title="Show password"></i>
                                    </div>
                                </div><!-- End .form-group -->

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-outline-primary-2 btn-block">
                                        <span>RESET PASSWORD</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>
                                </div><!-- End .form-footer -->
                            </form>
                        </div><!-- End .form-tab -->
                    </div><!-- End .form-box -->
                </div><!-- End .col-lg-5 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
@endsection
