@extends('layouts.insight')
@section('title', 'Checkout')
@section('subTitle', 'Checkout')

@section('style')
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
            <div class="container">
                <h1 class="page-title">Checkout</h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="checkout">
                <div class="container">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($appliedCoupon)
                        <div class="checkout-discount d-flex align-items-center flex-wrap" style="max-width:340px; gap:.75rem;">
                            <span class="text-success">Coupon <strong>{{ $appliedCoupon->coupon_code }}</strong> applied</span>
                            <a href="#" id="remove-coupon-btn" class="text-danger">Remove</a>
                        </div><!-- End .checkout-discount -->
                    @else
                        <div class="checkout-discount">
                            <form id="coupon-form">
                                <input type="text" name="coupon_code" id="checkout-discount-input" class="form-control" required>
                                <label for="checkout-discount-input" class="text-truncate">Have a coupon? <span>Click here to enter your code</span></label>
                            </form>
                        </div><!-- End .checkout-discount -->
                    @endif

                    @php
                        $userNameParts = $user ? explode(' ', $user->name, 2) : [null, null];
                    @endphp
                    <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-9">
                                <h2 class="checkout-title">Billing Details</h2><!-- End .checkout-title -->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>First Name *</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $userNameParts[0]) }}" required>
                                        @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div><!-- End .col-sm-6 -->

                                    <div class="col-sm-6">
                                        <label>Last Name *</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $userNameParts[1] ?? '') }}" required>
                                        @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div><!-- End .col-sm-6 -->
                                </div><!-- End .row -->

                                <label>Country *</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', 'India') }}" required>
                                @error('country') <div class="text-danger small">{{ $message }}</div> @enderror

                                <label>Street address *</label>
                                <input type="text" name="address_line1" class="form-control mb-2"
                                    placeholder="House number and Street name" value="{{ old('address_line1', $user->address ?? '') }}" required>
                                @error('address_line1') <div class="text-danger small">{{ $message }}</div> @enderror
                                <input type="text" name="address_line2" class="form-control"
                                    placeholder="Apartment, suite, unit etc. (optional)" value="{{ old('address_line2') }}">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Town / City *</label>
                                        <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                                        @error('city') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div><!-- End .col-sm-6 -->

                                    <div class="col-sm-6">
                                        <label>State *</label>
                                        <input type="text" name="state" class="form-control" value="{{ old('state') }}" required>
                                        @error('state') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div><!-- End .col-sm-6 -->
                                </div><!-- End .row -->

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Postcode / ZIP *</label>
                                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}" required>
                                        @error('postal_code') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div><!-- End .col-sm-6 -->

                                    <div class="col-sm-6">
                                        <label>Phone *</label>
                                        <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone_number ?? '') }}" required>
                                        @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div><!-- End .col-sm-6 -->
                                </div><!-- End .row -->

                                <label>Email address *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror

                                <label>Order notes (optional)</label>
                                <textarea name="order_notes" class="form-control" cols="30" rows="4"
                                    placeholder="Notes about your order, e.g. special notes for delivery">{{ old('order_notes') }}</textarea>

                                @guest
                                    @php
                                        // Opt-out rather than opt-in: checked by default on a fresh visit.
                                        // If the form was already submitted once (a validation error sent us
                                        // back here), respect whatever the user actually chose instead.
                                        $createAccountChecked = session()->hasOldInput() ? old('create_account', false) : true;
                                    @endphp
                                    <div class="p-3 mt-3" style="background:#FAF8F4; border-radius:8px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkout-create-account"
                                                name="create_account" value="1" {{ $createAccountChecked ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="checkout-create-account"><strong>Create an account</strong> — track this order, save your details, and check out faster next time</label>
                                        </div><!-- End .custom-checkbox -->

                                        <div class="form-group mt-2 mb-0" id="checkout-account-password-group"
                                            style="{{ $createAccountChecked ? '' : 'display:none;' }}">
                                            <label for="checkout-account-password">Password *</label>
                                            <div class="password-field-wrapper">
                                                <input type="password" class="form-control" id="checkout-account-password"
                                                    name="account_password" minlength="6">
                                                <i class="icon-eye password-toggle-icon" data-target="#checkout-account-password" title="Show password"></i>
                                            </div>
                                            @error('account_password') <div class="text-danger small">{{ $message }}</div> @enderror
                                        </div><!-- End .form-group -->
                                    </div>
                                @endguest
                            </div><!-- End .col-lg-9 -->
                            <aside class="col-lg-3">
                                <div class="summary">
                                    <h3 class="summary-title">Your Order</h3><!-- End .summary-title -->

                                    <table class="table table-summary">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($cartItems as $item)
                                                <tr>
                                                    <td>
                                                        {{ $item->product->product_name ?? 'Unavailable product' }}
                                                        <strong> × {{ $item->qty }}</strong>
                                                        @if ($item->color || $item->size)
                                                            <br><small class="text-muted">{{ optional($item->color)->color_name }} {{ optional($item->size)->size_name }}</small>
                                                        @endif
                                                    </td>
                                                    <td>₹{{ number_format($item->line_total, 2) }}</td>
                                                </tr>
                                            @endforeach

                                            <tr class="summary-subtotal">
                                                <td>Subtotal:</td>
                                                <td id="summary-subtotal">₹{{ number_format($totals['subtotal'], 2) }}</td>
                                            </tr><!-- End .summary-subtotal -->

                                            @if ($totals['discount'] > 0)
                                                <tr class="summary-discount">
                                                    <td>Discount ({{ $totals['coupon_code'] }}):</td>
                                                    <td id="summary-discount">-₹{{ number_format($totals['discount'], 2) }}</td>
                                                </tr>
                                            @endif

                                            @if (optional($appliedCoupon)->discount_type === 'free_shipping')
                                                <tr>
                                                    <td>Shipping:</td>
                                                    <td>Free (coupon applied)</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="free-shipping" name="shipping_display"
                                                                class="custom-control-input shipping-option" value="free"
                                                                {{ $totals['shipping_method'] === 'free' ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="free-shipping">Free Shipping — ₹0.00</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="standard-shipping" name="shipping_display"
                                                                class="custom-control-input shipping-option" value="standard"
                                                                {{ $totals['shipping_method'] === 'standard' ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="standard-shipping">Standard — ₹10.00</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="express-shipping" name="shipping_display"
                                                                class="custom-control-input shipping-option" value="express"
                                                                {{ $totals['shipping_method'] === 'express' ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="express-shipping">Express — ₹20.00</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr class="summary-total">
                                                <td>Total:</td>
                                                <td id="summary-total">₹{{ number_format($totals['cart_total'], 2) }}</td>
                                            </tr><!-- End .summary-total -->
                                        </tbody>
                                    </table><!-- End .table table-summary -->

                                    <div class="checkout-payment-method">
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="payment-cod" name="payment_method" class="custom-control-input" value="cod" checked>
                                            <label class="custom-control-label" for="payment-cod">Cash on Delivery</label>
                                        </div>
                                        <p class="text-muted small">Card / UPI / PayPal payment coming soon.</p>
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block">
                                        <span class="btn-text">Place Order</span>
                                        <span class="btn-hover-text">Place Order</span>
                                    </button>
                                </div><!-- End .summary -->
                            </aside><!-- End .col-lg-3 -->
                        </div><!-- End .row -->
                    </form>
                </div><!-- End .container -->
            </div><!-- End .checkout -->
        </div><!-- End .page-content -->
    </main><!-- End .main -->
@endsection

@section('script')
    <script>
        $(function () {
            $('#checkout-account-password').prop('required', $('#checkout-create-account').is(':checked'));

            $('#checkout-create-account').on('change', function () {
                $('#checkout-account-password-group').toggle(this.checked);
                $('#checkout-account-password').prop('required', this.checked);
            });

            $('#coupon-form').on('submit', function (e) {
                e.preventDefault();
                var code = $('#checkout-discount-input').val();

                $.ajax({
                    url: '{{ route('cart.coupon.apply') }}',
                    method: 'POST',
                    data: { coupon_code: code },
                    success: function (res) {
                        Swal.fire({ icon: 'success', title: res.message, timer: 1200, showConfirmButton: false })
                            .then(function () { window.location.reload(); });
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Failed to apply coupon';
                        Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                    }
                });
            });

            $('#remove-coupon-btn').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('cart.coupon.remove') }}',
                    method: 'DELETE',
                    success: function () {
                        window.location.reload();
                    }
                });
            });

            $(document).on('change', '.shipping-option', function () {
                $.ajax({
                    url: '{{ route('cart.shipping') }}',
                    method: 'POST',
                    data: { method: $(this).val() },
                    success: function () {
                        window.location.reload();
                    },
                    error: function () {
                        window.location.reload();
                    }
                });
            });
        });
    </script>
@endsection
