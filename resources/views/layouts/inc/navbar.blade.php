<!-- Sign in / Register Modal -->
<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="icon-close"></i></span>
                </button>

                <div class="form-box">
                    <div class="form-tab">
                        <ul class="nav nav-pills nav-fill" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin"
                                    role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab"
                                    aria-controls="register" aria-selected="false">Register</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="tab-content-5">
                            <div class="tab-pane fade show active" id="signin" role="tabpanel"
                                aria-labelledby="signin-tab">
                                <div class="auth-alert"></div>
                                <form id="signin-form" action="{{ route('login.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="singin-email">Email address *</label>
                                        <input type="email" class="form-control" id="singin-email" name="email"
                                            required>
                                    </div><!-- End .form-group -->

                                    <div class="form-group">
                                        <label for="singin-password">Password *</label>
                                        <div class="password-field-wrapper">
                                            <input type="password" class="form-control" id="singin-password"
                                                name="password" required>
                                            <i class="icon-eye password-toggle-icon" data-target="#singin-password" title="Show password"></i>
                                        </div>
                                    </div><!-- End .form-group -->

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>LOG IN</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="signin-remember"
                                                name="remember_me" value="1">
                                            <label class="custom-control-label" for="signin-remember">Remember
                                                Me</label>
                                        </div><!-- End .custom-checkbox -->

                                        <a href="#" class="forgot-link" id="forgot-password-link">Forgot Your Password?</a>
                                    </div><!-- End .form-footer -->
                                </form>
                                <div class="form-choice">
                                    <p class="text-center">or sign in with</p>
                                    <div class="row justify-content-center">
                                        <div class="col-sm-6">
                                            <a href="{{ route('auth.google.redirect') }}" class="btn btn-login btn-g">
                                                <i class="icon-google"></i>
                                                Login With Google
                                            </a>
                                        </div><!-- End .col-6 -->
                                    </div><!-- End .row -->
                                </div><!-- End .form-choice -->
                            </div><!-- .End .tab-pane -->
                            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                <div class="auth-alert"></div>
                                <form id="register-form" action="{{ route('register.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="register-name">Your Name *</label>
                                        <input type="text" class="form-control" id="register-name"
                                            name="name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="register-email">Your email address *</label>
                                        <input type="email" class="form-control" id="register-email"
                                            name="email" required>
                                    </div><!-- End .form-group -->

                                    <div class="form-group">
                                        <label for="register-password">Password *</label>
                                        <div class="password-field-wrapper">
                                            <input type="password" class="form-control" id="register-password"
                                                name="password" required minlength="6">
                                            <i class="icon-eye password-toggle-icon" data-target="#register-password" title="Show password"></i>
                                        </div>
                                    </div><!-- End .form-group -->

                                    <div class="form-group">
                                        <label for="register-mobile">Mobile No. *</label>
                                        <input type="text" class="form-control" id="register-mobile"
                                            name="phone_number" required>
                                    </div>

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>SIGN UP</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="register-policy"
                                                name="terms" value="1" required>
                                            <label class="custom-control-label" for="register-policy">I agree to
                                                the <a href="#">privacy policy</a> *</label>
                                        </div><!-- End .custom-checkbox -->
                                    </div><!-- End .form-footer -->
                                </form>
                                <div class="form-choice">
                                    <p class="text-center">or sign in with</p>
                                    <div class="row justify-content-center">
                                        <div class="col-sm-6">
                                            <a href="{{ route('auth.google.redirect') }}" class="btn btn-login btn-g">
                                                <i class="icon-google"></i>
                                                Login With Google
                                            </a>
                                        </div><!-- End .col-6 -->
                                    </div><!-- End .row -->
                                </div><!-- End .form-choice -->
                            </div><!-- .End .tab-pane -->
                        </div><!-- End .tab-content -->

                        <div class="otp-step" id="otp-step" style="display:none;">
                            <div class="auth-alert"></div>
                            <p class="text-center">We've sent a 6-digit verification code to your email and phone.
                                Enter it below to continue &mdash; either code works.</p>
                            <form id="otp-form" action="{{ route('otp.verify') }}" method="POST">
                                @csrf
                                <div class="form-group text-center">
                                    <label for="otp-code" class="d-block">Verification Code *</label>
                                    <input type="text" class="form-control" id="otp-code" name="otp"
                                        maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                        autocomplete="one-time-code"
                                        style="max-width:220px; margin:0 auto; text-align:center; font-size:22px; font-weight:600; letter-spacing:10px; text-indent:10px;"
                                        required>
                                </div><!-- End .form-group -->

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-outline-primary-2 btn-block">
                                        <span>VERIFY</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>

                                    <p class="text-center mt-3">Didn't get a code?
                                        <a href="#" id="otp-resend-link">Resend code</a>
                                    </p>
                                </div><!-- End .form-footer -->
                            </form>
                        </div><!-- End .otp-step -->

                        <div class="forgot-password-step" id="forgot-password-step" style="display:none;">
                            <div class="auth-alert"></div>
                            <p class="text-center">Enter your email and we'll send you a link to reset your
                                password.</p>
                            <form id="forgot-password-form" action="{{ route('password.email') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="forgot-email">Email address *</label>
                                    <input type="email" class="form-control" id="forgot-email" name="email" required>
                                </div><!-- End .form-group -->

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-outline-primary-2 btn-block">
                                        <span>SEND RESET LINK</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>

                                    <p class="text-center mt-3">
                                        <a href="#" id="back-to-signin-link">Back to Sign In</a>
                                    </p>
                                </div><!-- End .form-footer -->
                            </form>
                        </div><!-- End .forgot-password-step -->
                    </div><!-- End .form-tab -->
                </div><!-- End .form-box -->
            </div><!-- End .modal-body -->
        </div><!-- End .modal-content -->
    </div><!-- End .modal-dialog -->
</div><!-- End .modal -->

@push('scripts')
<script>
    $(function () {
        function showOtpStep() {
            $('#tab-content-5, .form-tab > .nav-pills, #forgot-password-step').hide();
            $('#otp-step').show();
            $('#otp-code').val('').focus();
        }

        function showForgotPasswordStep() {
            $('#tab-content-5, .form-tab > .nav-pills').hide();
            $('#forgot-password-step').show();
            $('#forgot-email').val('').focus();
        }

        function showSigninStep() {
            $('#forgot-password-step, #otp-step').hide();
            $('#tab-content-5, .form-tab > .nav-pills').show();
            $('#signin-tab').tab('show');
        }

        function showAlert($scope, type, message) {
            $scope.find('.auth-alert').html(
                $('<div class="alert alert-' + type + '"></div>').text(message)
            );
        }

        function submitAuthForm($form) {
            var $scope = $form.closest('.tab-pane, .otp-step, .forgot-password-step');
            var $submitBtn = $form.find('button[type="submit"]');

            if ($submitBtn.prop('disabled')) {
                return;
            }

            $scope.find('.auth-alert').empty();
            $submitBtn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    if (res.data && res.data.step === 'otp') {
                        Swal.fire({ icon: 'info', title: res.message, timer: 1800, showConfirmButton: false });
                        showOtpStep();
                        return;
                    }

                    if ($form.attr('id') === 'forgot-password-form') {
                        showAlert($scope, 'success', res.message);
                        $submitBtn.prop('disabled', false);
                        return;
                    }

                    Swal.fire({ icon: 'success', title: res.message, timer: 1200, showConfirmButton: false })
                        .then(function () { window.location.reload(); });
                },
                error: function (xhr) {
                    var res = xhr.responseJSON || {};
                    var errors = res.validation || {};
                    var firstError = Object.values(errors)[0];
                    showAlert($scope, 'danger', firstError || res.message || 'Something went wrong');
                    $submitBtn.prop('disabled', false);
                }
            });
        }

        $('#signin-form, #register-form, #otp-form, #forgot-password-form').on('submit', function (e) {
            e.preventDefault();
            submitAuthForm($(this));
        });

        $('#forgot-password-link').on('click', function (e) {
            e.preventDefault();
            showForgotPasswordStep();
        });

        $('#back-to-signin-link').on('click', function (e) {
            e.preventDefault();
            showSigninStep();
        });

        $('#otp-resend-link').on('click', function (e) {
            e.preventDefault();
            var $link = $(this);

            if ($link.hasClass('disabled')) {
                return;
            }

            var $scope = $('#otp-step');
            $scope.find('.auth-alert').empty();
            $link.addClass('disabled');

            $.ajax({
                url: '{{ route('otp.resend') }}',
                method: 'POST',
                success: function (res) {
                    showAlert($scope, 'success', res.message);
                    $link.removeClass('disabled');
                },
                error: function (xhr) {
                    var res = xhr.responseJSON || {};
                    showAlert($scope, 'danger', res.message || 'Failed to resend code');
                    $link.removeClass('disabled');
                }
            });
        });

        $('[data-auth-tab]').on('click', function () {
            $('#' + $(this).data('auth-tab')).tab('show');
        });

        @if (session('open_auth_modal'))
            @if (session('open_auth_modal') === 'otp')
                showOtpStep();
            @else
                $('#{{ session('open_auth_modal') === 'register' ? 'register-tab' : 'signin-tab' }}').tab('show');
            @endif
            $('#signin-modal').modal('show');
        @endif
    });
</script>
@endpush

<div class="container newsletter-popup-container mfp-hide" id="newsletter-popup-form">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="row no-gutters bg-white newsletter-popup-content">
                <div class="col-xl-3-5col col-lg-7 banner-content-wrap">
                    <div class="banner-content text-center">
                        <img src="{{ asset('vivek_logo.png') }}" class="logo" alt="logo" width="60"
                            height="15">
                        <h2 class="banner-title">get <span>25<light>%</light></span> off</h2>
                        <p>Subscribe to the SelfBuy eCommerce newsletter to receive timely updates from your
                            favorite
                            products.</p>
                        <form action="#">
                            <div class="input-group input-group-round">
                                <input type="email" class="form-control form-control-white"
                                    placeholder="Your Email Address" aria-label="Email Adress" required>
                                <div class="input-group-append">
                                    <button class="btn" type="submit"><span>go</span></button>
                                </div><!-- .End .input-group-append -->
                            </div><!-- .End .input-group -->
                        </form>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="register-policy-2" required>
                            <label class="custom-control-label" for="register-policy-2">Do not show this popup
                                again</label>
                        </div><!-- End .custom-checkbox -->
                    </div>
                </div>
                <div class="col-xl-2-5col col-lg-5 ">
                    <img src="{{ asset('assets/images/popup/newsletter/img-1.jpg') }}" class="newsletter-img"
                        alt="newsletter">
                </div>
            </div>
        </div>
    </div>
</div>
