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
                                        <button type="submit" class="btn btn-outline-primary-2" data-busy-text="LOGGING IN...">
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
                                                name="password" required minlength="8">
                                            <i class="icon-eye password-toggle-icon" data-target="#register-password" title="Show password"></i>
                                        </div>
                                    </div><!-- End .form-group -->

                                    <div class="form-group">
                                        <label for="register-mobile">Mobile No. *</label>
                                        <input type="text" class="form-control" id="register-mobile"
                                            name="phone_number" required>
                                    </div>

                                    <div class="form-footer auth-footer-start">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="register-policy"
                                                name="terms" value="1" required>
                                            <label class="custom-control-label" for="register-policy">I agree to
                                                the <a href="{{ route('privacy_policy') }}" target="_blank" rel="noopener">privacy policy</a> *</label>
                                        </div><!-- End .custom-checkbox -->

                                        <button type="submit" class="btn btn-outline-primary-2" data-busy-text="CREATING ACCOUNT...">
                                            <span>SIGN UP</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>
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

                        <style>
                            /* Auth modal footers stacked in a column instead of the theme's space-between row
                               (which pushed button and text out of line). Selectors out-rank the theme's
                               .form-tab .form-footer .btn { margin-right; order } rules.
                               .auth-footer-center: OTP + forgot password (centred steps)
                               .auth-footer-start:  register (terms checkbox above the button, left-aligned) */
                            .form-tab .form-footer.auth-footer-center,
                            .form-tab .form-footer.auth-footer-start { flex-direction: column; flex-wrap: nowrap; }
                            .form-tab .form-footer.auth-footer-center { align-items: center; justify-content: center; }
                            .form-tab .form-footer.auth-footer-start { align-items: flex-start; }
                            .form-tab .form-footer.auth-footer-center .btn { order: 0; width: 100%; max-width: 220px; margin: 0; }
                            .form-tab .form-footer.auth-footer-center .auth-footer-note { order: 1; margin: 1.6rem 0 0; text-align: center; }
                            .form-tab .form-footer.auth-footer-start .custom-control { order: 0; margin: 0 0 1.6rem; }
                            .form-tab .form-footer.auth-footer-start .btn { order: 1; margin: 0; min-width: 170px; }
                            #otp-resend-link.disabled { color: #999; pointer-events: none; text-decoration: none; cursor: default; }
                            .form-tab .form-footer .btn[aria-busy="true"] { opacity: .75; cursor: wait; }
                        </style>
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
                                        style="max-width:220px; margin:0 auto; text-align:center; font-size:22px; font-weight:600; letter-spacing:10px; padding-left:calc(1rem + 10px);"
                                        required>
                                </div><!-- End .form-group -->

                                <div class="form-footer auth-footer-center">
                                    <button type="submit" class="btn btn-outline-primary-2" data-busy-text="VERIFYING...">
                                        <span>VERIFY</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>

                                    <p class="auth-footer-note">Didn't get a code?
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

                                <div class="form-footer auth-footer-center">
                                    <button type="submit" class="btn btn-outline-primary-2" data-busy-text="SENDING...">
                                        <span>SEND RESET LINK</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>

                                    <p class="auth-footer-note">
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
        // ── Resend-code countdown (matches OtpService::RESEND_COOLDOWN_SECONDS) ──
        var RESEND_COOLDOWN = {{ \App\Services\OtpService::RESEND_COOLDOWN_SECONDS }};
        var resendTimer = null;

        function startResendCountdown(seconds) {
            var $link = $('#otp-resend-link');
            clearInterval(resendTimer);

            var remaining = seconds;
            var tick = function () {
                if (remaining <= 0) {
                    clearInterval(resendTimer);
                    $link.removeClass('disabled').removeAttr('aria-disabled').text('Resend code');
                    return;
                }
                $link.addClass('disabled').attr('aria-disabled', 'true').text('Resend code in ' + remaining + 's');
                remaining--;
            };

            tick();
            resendTimer = setInterval(tick, 1000);
        }

        // Button shows e.g. "LOGGING IN..." and is locked while a request is in flight.
        function setBusy($btn, busy) {
            var $label = $btn.find('span').first();

            if (busy) {
                if ($btn.data('idle-text') === undefined) {
                    $btn.data('idle-text', $label.text());
                }
                $label.text($btn.data('busy-text') || 'PLEASE WAIT...');
                $btn.prop('disabled', true).attr('aria-busy', 'true').find('i').hide();
            } else {
                if ($btn.data('idle-text') !== undefined) {
                    $label.text($btn.data('idle-text'));
                }
                $btn.prop('disabled', false).removeAttr('aria-busy').find('i').show();
            }
        }

        function showOtpStep() {
            $('#tab-content-5, .form-tab > .nav-pills, #forgot-password-step').hide();
            $('#otp-step').show();
            $('#otp-code').val('').focus();
            // A code has just been sent - resending is only possible after the cooldown.
            startResendCountdown(RESEND_COOLDOWN);
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

        // After sign-in: go to the page that asked for it (server sends data.redirect), else reload.
        function goAfterAuth(res) {
            if (res && res.data && res.data.redirect) {
                window.location.href = res.data.redirect;
            } else {
                window.location.reload();
            }
        }

        function submitAuthForm($form) {
            var $scope = $form.closest('.tab-pane, .otp-step, .forgot-password-step');
            var $submitBtn = $form.find('button[type="submit"]');

            if ($submitBtn.prop('disabled')) {
                return;
            }

            $scope.find('.auth-alert').empty();
            setBusy($submitBtn, true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    if (res.data && res.data.step === 'otp') {
                        setBusy($submitBtn, false);
                        Swal.fire({ icon: 'info', title: res.message, timer: 1800, showConfirmButton: false });
                        showOtpStep();
                        return;
                    }

                    if ($form.attr('id') === 'forgot-password-form') {
                        showAlert($scope, 'success', res.message);
                        setBusy($submitBtn, false);
                        return;
                    }

                    // Logged in: keep the button locked ("VERIFYING...") until the page reloads.

                    // New account: show the welcome coupon and wait for the customer before reloading.
                    if (res.data && res.data.welcome_coupon) {
                        var coupon = res.data.welcome_coupon;
                        var $html = $('<div>')
                            .append($('<p>').text('Here is your welcome code for ' + coupon.percent + '% off your first order:'))
                            .append($('<p>').css({ fontSize: '2.4rem', fontWeight: 700, letterSpacing: '.3rem', border: '2px dashed #c96', padding: '.8rem', margin: '1.2rem 0' }).text(coupon.code))
                            .append($('<p>').css({ fontSize: '1.3rem', color: '#777' }).text('Enter it in your cart at checkout. We have also emailed it to you.'));

                        Swal.fire({ icon: 'success', title: 'Welcome! Your account is verified.', html: $html.html(), confirmButtonText: 'Start Shopping' })
                            .then(function () { goAfterAuth(res); });
                        return;
                    }

                    Swal.fire({ icon: 'success', title: res.message, timer: 1200, showConfirmButton: false })
                        .then(function () { goAfterAuth(res); });
                },
                error: function (xhr) {
                    var res = xhr.responseJSON || {};
                    var errors = res.validation || {};
                    var firstError = Object.values(errors)[0];
                    showAlert($scope, 'danger', firstError || res.message || 'Something went wrong');
                    setBusy($submitBtn, false);
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
            $link.addClass('disabled').attr('aria-disabled', 'true').text('Sending...');

            $.ajax({
                url: '{{ route('otp.resend') }}',
                method: 'POST',
                success: function (res) {
                    showAlert($scope, 'success', res.message);
                    $('#otp-code').val('').focus();
                    startResendCountdown(RESEND_COOLDOWN);
                },
                error: function (xhr) {
                    var res = xhr.responseJSON || {};
                    var message = res.message || 'Failed to resend code';
                    showAlert($scope, 'danger', message);

                    // Server-side cooldown ("Please wait 12s ...") - count down from what it says.
                    var wait = parseInt((message.match(/(\d+)s/) || [])[1], 10);
                    if (wait > 0) {
                        startResendCountdown(wait);
                    } else {
                        $link.removeClass('disabled').removeAttr('aria-disabled').text('Resend code');
                    }
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
                @if (session('error'))
                    {{-- e.g. "Please sign in to view your order." (storefront pages have no global flash area) --}}
                    $('#signin .auth-alert').html($('<div class="alert alert-warning"></div>').text(@json(session('error'))));
                @endif
            @endif
            $('#signin-modal').modal('show');
        @endif
    });
</script>
@endpush

{{--
    Newsletter popup (text, offer, image, delay: Admin > Home Settings > Newsletter Popup).
    Not rendered at all when the visitor subscribed, ticked "Do not show again", or closed it
    within the last day (sb_newsletter_popup cookie), when a logged-in customer is already
    subscribed, or during cart/checkout/payment - see NewsletterController::shouldShowPopup().
--}}
@if (\App\Http\Controllers\NewsletterController::shouldShowPopup(request()))
@php
    $popupPercent = home_setting('newsletter_popup_offer_percent');
    $popupPercent = is_numeric($popupPercent) ? rtrim(rtrim(number_format((float) $popupPercent, 2, '.', ''), '0'), '.') : null;
@endphp
<div class="container newsletter-popup-container mfp-hide" id="newsletter-popup-form"
    data-delay="{{ max(0, (int) home_setting('newsletter_popup_delay', 5)) * 1000 }}">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="row no-gutters bg-white newsletter-popup-content">
                <div class="col-xl-3-5col col-lg-7 banner-content-wrap">
                    <div class="banner-content text-center">
                        <img src="{{ asset('vivek_logo.png') }}" class="logo" alt="logo" width="60"
                            height="15">
                        <h2 class="banner-title">
                            {{ home_setting('newsletter_popup_offer_prefix') }}
                            @if ($popupPercent !== null)
                                <span>{{ $popupPercent }}<light>%</light></span>
                            @endif
                            {{ home_setting('newsletter_popup_offer_text') }}
                        </h2>
                        @if (home_setting('newsletter_popup_description'))
                            <p>{{ home_setting('newsletter_popup_description') }}</p>
                        @endif

                        <form id="newsletter-popup-subscribe" action="{{ route('newsletter.subscribe') }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="newsletter_form" value="1">
                            {{-- Honeypot - hidden from people, bots fill it in. --}}
                            <input type="text" name="website" value="" tabindex="-1" autocomplete="off"
                                style="position:absolute; left:-9999px;" aria-hidden="true">

                            <div class="input-group input-group-round">
                                <input type="email" name="email" id="newsletter-popup-email" class="form-control form-control-white"
                                    placeholder="Your Email Address" aria-label="Email Address" maxlength="190" required>
                                <div class="input-group-append">
                                    <button class="btn" type="submit" id="newsletter-popup-submit"><span>go</span></button>
                                </div><!-- .End .input-group-append -->
                            </div><!-- .End .input-group -->
                            <div class="newsletter-popup-feedback text-left" id="newsletter-popup-feedback" role="alert" aria-live="polite"></div>
                        </form>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="newsletter-popup-dont-show">
                            <label class="custom-control-label" for="newsletter-popup-dont-show">Do not show this popup
                                again</label>
                        </div><!-- End .custom-checkbox -->
                    </div>
                </div>
                <div class="col-xl-2-5col col-lg-5 ">
                    <img src="{{ home_asset('newsletter_popup_image') }}" class="newsletter-img"
                        alt="newsletter">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .newsletter-popup-feedback { font-size: 1.3rem; min-height: 2rem; margin-top: .6rem; padding-left: 2rem; }
    .newsletter-popup-feedback.is-error { color: #e53935; }
    .newsletter-popup-feedback.is-success { color: #2e7d32; }
    #newsletter-popup-email.is-invalid { border-color: #e53935; }
</style>
<script>
    (function ($) {
        var COOKIE = 'sb_newsletter_popup';
        var OPEN_DELAY_MS = parseInt($('#newsletter-popup-form').data('delay'), 10) || 0; // set in Admin > Home Settings
        // How long each outcome keeps the popup away.
        var HIDE_DAYS = { dismissed: 1, never: 365, subscribed: 365 };

        function getCookie() {
            var match = document.cookie.match(new RegExp('(?:^|; )' + COOKIE + '=([^;]*)'));
            return match ? decodeURIComponent(match[1]) : null;
        }

        function setCookie(value) {
            var expires = new Date(Date.now() + HIDE_DAYS[value] * 864e5).toUTCString();
            document.cookie = COOKIE + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/; SameSite=Lax';
        }

        function clearCookie() {
            document.cookie = COOKIE + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
        }

        var $popup    = $('#newsletter-popup-form');
        var $form     = $('#newsletter-popup-subscribe');
        var $email    = $('#newsletter-popup-email');
        var $submit   = $('#newsletter-popup-submit');
        var $feedback = $('#newsletter-popup-feedback');
        var $dontShow = $('#newsletter-popup-dont-show');
        var subscribed = false;

        function feedback(message, type) {
            $feedback.removeClass('is-error is-success').addClass(type ? 'is-' + type : '').text(message || '');
            $email.toggleClass('is-invalid', type === 'error');
        }

        function anotherPopupIsOpen() {
            return $('.modal.show').length > 0 || ($.magnificPopup && $.magnificPopup.instance.isOpen);
        }

        function openPopup() {
            if (!$.fn.magnificPopup || getCookie() || anotherPopupIsOpen()) {
                return;
            }

            $.magnificPopup.open({
                items: { src: '#newsletter-popup-form' },
                type: 'inline',
                removalDelay: 350,
                callbacks: {
                    open: function () {
                        $('body').css('overflow-x', 'visible');
                        $('.sticky-header.fixed').css('padding-right', '1.7rem');
                    },
                    close: function () {
                        $('body').css('overflow-x', 'hidden');
                        $('.sticky-header.fixed').css('padding-right', '0');

                        // Closed without subscribing or opting out: ask again tomorrow.
                        if (!subscribed && !$dontShow.is(':checked')) {
                            setCookie('dismissed');
                        }
                    }
                }
            }, 0);
        }

        if (!$popup.length) {
            return;
        }

        // Auto-open only when the visitor hasn't opted out / subscribed / recently closed it.
        // The handlers below are always bound, so the form works however the popup gets opened.
        if (!getCookie()) {
            setTimeout(openPopup, OPEN_DELAY_MS);
        }

        // "Do not show this popup again" - takes effect as soon as it is ticked.
        $dontShow.on('change', function () {
            this.checked ? setCookie('never') : clearCookie();
        });

        $email.on('input', function () {
            if ($email.hasClass('is-invalid')) feedback('');
        });

        $form.on('submit', function (e) {
            e.preventDefault();

            var email = $.trim($email.val());
            $email.val(email);

            if (!email) {
                feedback('Please enter your email address.', 'error');
                $email.trigger('focus');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email) || email.length > 190) {
                feedback('Please enter a valid email address.', 'error');
                $email.trigger('focus');
                return;
            }

            $submit.prop('disabled', true);
            feedback('Subscribing...');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (res) {
                    subscribed = true;
                    setCookie('subscribed');
                    feedback(res.message, 'success');
                    $form.find('.input-group').hide();
                    setTimeout(function () { $.magnificPopup.close(); }, 2500);
                },
                error: function (xhr) {
                    var res = xhr.responseJSON || {};
                    var message = 'Something went wrong. Please try again.';

                    if (xhr.status === 422 && res.errors) {
                        message = (res.errors.email || res.errors.website || [res.message])[0];
                    } else if (xhr.status === 429) {
                        message = 'Too many attempts. Please wait a minute and try again.';
                    } else if (xhr.status === 419) {
                        message = 'Your session expired. Please refresh the page and try again.';
                    }

                    feedback(message, 'error');
                },
                complete: function () {
                    $submit.prop('disabled', false);
                }
            });
        });
    })(jQuery);
</script>
@endpush
@endif

{{-- Result of a plain (non-AJAX) newsletter post - only if the popup JS failed mid-way.
     Outside the popup block: after subscribing, the popup itself is no longer rendered. --}}
@if (session('newsletter_message') || ($errors->has('email') && old('newsletter_form')))
@push('scripts')
<script>
    $(function () {
        @if (session('newsletter_message'))
            Swal.fire({ icon: 'success', title: @json(session('newsletter_message')), timer: 2500, showConfirmButton: false });
        @else
            Swal.fire({ icon: 'error', title: 'Oops...', text: @json($errors->first('email')) });
        @endif
    });
</script>
@endpush
@endif
