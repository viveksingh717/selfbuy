@extends('layouts.insight')
@section('title', 'My Account')
@section('subTitle', 'Manage your account settings and preferences')

@section('style')
@endsection

@section('content')
    <main class="main">
        <div class="page-header text-center" style="background-image: url({{ asset('assets/images/page-header-bg.jpg') }})">
            <div class="container">
                <h1 class="page-title">My Account<span>Manage your account settings and preferences</span></h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Account</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="dashboard">
                <div class="container">
                    <div class="row">
                        <aside class="col-md-4 col-lg-3">
                            <ul class="nav nav-dashboard flex-column mb-3 mb-md-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab-dashboard-link" data-toggle="tab" href="#tab-dashboard" role="tab" aria-controls="tab-dashboard" aria-selected="true">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-orders-link" data-toggle="tab" href="#tab-orders" role="tab" aria-controls="tab-orders" aria-selected="false">Orders</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-address-link" data-toggle="tab" href="#tab-address" role="tab" aria-controls="tab-address" aria-selected="false">Address</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-account-link" data-toggle="tab" href="#tab-account" role="tab" aria-controls="tab-account" aria-selected="false">Account Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-password-link" data-toggle="tab" href="#tab-password" role="tab" aria-controls="tab-password" aria-selected="false">Change Password</a>
                                </li>
                                <li class="nav-item">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="nav-link" style="width:100%; text-align:left; background:none; border:none; border-bottom:.1rem solid #ebebeb; cursor:pointer;">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </aside><!-- End .col-lg-3 -->

                        <div class="col-md-8 col-lg-9">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel" aria-labelledby="tab-dashboard-link">
                                    <p>Hello <span class="font-weight-normal text-dark">{{ $user->name }}</span>.</p>

                                    <div class="row">
                                        <div class="col-6 col-md-3">
                                            <div class="card card-dashboard text-center h-100">
                                                <div class="card-body d-flex flex-column justify-content-center">
                                                    <h2 class="mb-0">{{ $totalOrders }}</h2>
                                                    <p class="text-muted mb-0">Total Orders</p>
                                                </div>
                                            </div>
                                        </div><!-- End .col -->

                                        <div class="col-6 col-md-3">
                                            <div class="card card-dashboard text-center h-100">
                                                <div class="card-body d-flex flex-column justify-content-center">
                                                    <h2 class="mb-0">₹{{ number_format($totalSpent, 0) }}</h2>
                                                    <p class="text-muted mb-0">Total Spent</p>
                                                </div>
                                            </div>
                                        </div><!-- End .col -->

                                        <div class="col-6 col-md-3">
                                            <div class="card card-dashboard text-center h-100">
                                                <div class="card-body d-flex flex-column justify-content-center">
                                                    <h2 class="mb-0">{{ $paidOrdersCount }}</h2>
                                                    <p class="text-muted mb-0">Paid Orders</p>
                                                </div>
                                            </div>
                                        </div><!-- End .col -->

                                        <div class="col-6 col-md-3">
                                            <div class="card card-dashboard text-center h-100">
                                                <div class="card-body d-flex flex-column justify-content-center">
                                                    <h2 class="mb-0">{{ $pendingPaymentOrdersCount }}</h2>
                                                    <p class="text-muted mb-0">Awaiting Payment</p>
                                                </div>
                                            </div>
                                        </div><!-- End .col -->
                                    </div><!-- End .row -->

                                    @if ($recentOrders->isNotEmpty())
                                        <h3 class="card-title mb-2 mt-2">Recent Orders</h3>
                                        <div class="table-responsive">
                                            <table class="table table-mobile">
                                                <thead>
                                                    <tr>
                                                        <th>Order</th>
                                                        <th>Date</th>
                                                        <th>Payment</th>
                                                        <th>Total</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($recentOrders as $order)
                                                        <tr>
                                                            <td>{{ $order->order_number }}</td>
                                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                                            <td>{{ ucfirst($order->payment_status) }}</td>
                                                            <td>₹{{ number_format($order->total, 2) }}</td>
                                                            <td><a href="{{ route('checkout.success', $order->order_number) }}" class="btn btn-outline-primary-2 btn-sm">View</a></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @if ($totalOrders > $recentOrders->count())
                                            <a href="#tab-orders" class="tab-trigger-link link-underline">View all {{ $totalOrders }} orders</a>
                                        @endif
                                    @else
                                        <p>You haven't placed any orders yet.</p>
                                        <a href="{{ route('home') }}" class="btn btn-outline-primary-2"><span>START SHOPPING</span><i class="icon-long-arrow-right"></i></a>
                                    @endif
                                </div><!-- .End .tab-pane -->

                                <div class="tab-pane fade" id="tab-orders" role="tabpanel" aria-labelledby="tab-orders-link">
                                    @if ($orders->isEmpty())
                                        <p>No order has been made yet.</p>
                                        <a href="{{ route('home') }}" class="btn btn-outline-primary-2"><span>GO SHOP</span><i class="icon-long-arrow-right"></i></a>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-mobile">
                                                <thead>
                                                    <tr>
                                                        <th>Order</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Total</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($orders as $order)
                                                        <tr>
                                                            <td>{{ $order->order_number }}</td>
                                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                                            <td>{{ ucfirst($order->order_status) }}</td>
                                                            <td>₹{{ number_format($order->total, 2) }}</td>
                                                            <td><a href="{{ route('checkout.success', $order->order_number) }}" class="btn btn-outline-primary-2 btn-sm">View</a></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div><!-- .End .tab-pane -->

                                <div class="tab-pane fade" id="tab-address" role="tabpanel" aria-labelledby="tab-address-link">
                                    <p>This address is used to prefill your shipping details at checkout.</p>

                                    @php
                                        $addr = [
                                            'address_line1' => $user->address_line1 ?? optional($lastOrder)->address_line1,
                                            'address_line2' => $user->address_line2 ?? optional($lastOrder)->address_line2,
                                            'city' => $user->city ?? optional($lastOrder)->city,
                                            'state' => $user->state ?? optional($lastOrder)->state,
                                            'postal_code' => $user->postal_code ?? optional($lastOrder)->postal_code,
                                            'country' => $user->country ?? optional($lastOrder)->country ?? 'India',
                                        ];
                                    @endphp

                                    <form id="address-form" action="{{ route('account.address.update') }}" method="POST">
                                        @csrf
                                        <label>Address Line 1 *</label>
                                        <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $addr['address_line1']) }}" required>

                                        <label>Address Line 2</label>
                                        <input type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $addr['address_line2']) }}">

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>City *</label>
                                                <input type="text" name="city" class="form-control" value="{{ old('city', $addr['city']) }}" required>
                                            </div><!-- End .col-sm-6 -->

                                            <div class="col-sm-6">
                                                <label>State *</label>
                                                <input type="text" name="state" class="form-control" value="{{ old('state', $addr['state']) }}" required>
                                            </div><!-- End .col-sm-6 -->
                                        </div><!-- End .row -->

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Postal Code *</label>
                                                <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $addr['postal_code']) }}" required>
                                            </div><!-- End .col-sm-6 -->

                                            <div class="col-sm-6">
                                                <label>Country *</label>
                                                <input type="text" name="country" class="form-control mb-2" value="{{ old('country', $addr['country']) }}" required>
                                            </div><!-- End .col-sm-6 -->
                                        </div><!-- End .row -->

                                        @if ($errors->address->any())
                                            <div class="text-danger small mb-2">{{ $errors->address->first() }}</div>
                                        @endif

                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>SAVE ADDRESS</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>
                                    </form>
                                </div><!-- .End .tab-pane -->

                                <div class="tab-pane fade" id="tab-account" role="tabpanel" aria-labelledby="tab-account-link">
                                    <form id="account-details-form" action="{{ route('account.details.update') }}" method="POST">
                                        @csrf
                                        <label>Name *</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>

                                        <label>Email address *</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>

                                        <label>Phone number *</label>
                                        <input type="text" name="phone_number" class="form-control mb-2" value="{{ old('phone_number', $user->phone_number) }}" required>

                                        @if ($errors->accountDetails->any())
                                            <div class="text-danger small mb-2">{{ $errors->accountDetails->first() }}</div>
                                        @endif

                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>SAVE CHANGES</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>
                                    </form>
                                </div><!-- .End .tab-pane -->

                                <div class="tab-pane fade" id="tab-password" role="tabpanel" aria-labelledby="tab-password-link">
                                    <form id="change-password-form" action="{{ route('account.password.update') }}" method="POST">
                                        @csrf
                                        <label>Current password *</label>
                                        <div class="password-field-wrapper">
                                            <input type="password" name="current_password" id="current-password" class="form-control" required>
                                            <i class="icon-eye password-toggle-icon" data-target="#current-password" title="Show password"></i>
                                        </div>

                                        <label>New password *</label>
                                        <div class="password-field-wrapper">
                                            <input type="password" name="password" id="new-password" class="form-control" required minlength="8">
                                            <i class="icon-eye password-toggle-icon" data-target="#new-password" title="Show password"></i>
                                        </div>

                                        <label>Confirm new password *</label>
                                        <div class="password-field-wrapper mb-2">
                                            <input type="password" name="password_confirmation" id="confirm-new-password" class="form-control" required minlength="8">
                                            <i class="icon-eye password-toggle-icon" data-target="#confirm-new-password" title="Show password"></i>
                                        </div>

                                        @if ($errors->changePassword->any())
                                            <div class="text-danger small mb-2">{{ $errors->changePassword->first() }}</div>
                                        @endif

                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>CHANGE PASSWORD</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>
                                    </form>
                                </div><!-- .End .tab-pane -->
                            </div>
                        </div><!-- End .col-lg-9 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .dashboard -->
        </div><!-- End .page-content -->
    </main>
@endsection

@section('script')
    <script>
        $(function () {
            // .tab-trigger-link (the "recent orders" etc. links inside the
            // Dashboard tab) is already wired up in main.js — nothing to add here.

            function submitAccountForm($form, successMessage, resetOnSuccess) {
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (res) {
                        if (!res.success) {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: res.message });
                            return;
                        }

                        Swal.fire({ icon: 'success', title: successMessage, timer: 1500, showConfirmButton: false });

                        // Password fields should clear after a successful change (nothing
                        // sensitive left sitting visible); data fields like name/address
                        // should keep showing what was just saved, not revert to the
                        // page-load value that .reset() would restore.
                        if (resetOnSuccess) {
                            $form[0].reset();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.validation) {
                            var firstError = Object.values(xhr.responseJSON.validation)[0];
                            Swal.fire({ icon: 'error', title: 'Oops...', text: firstError });
                            return;
                        }

                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong. Please try again.';
                        Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                    }
                });
            }

            $('#account-details-form').on('submit', function (e) {
                e.preventDefault();
                submitAccountForm($(this), 'Account details updated', false);
            });

            $('#change-password-form').on('submit', function (e) {
                e.preventDefault();

                if ($('#new-password').val() !== $('#confirm-new-password').val()) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'New password and confirmation do not match.' });
                    return;
                }

                submitAccountForm($(this), 'Password changed', true);
            });

            $('#address-form').on('submit', function (e) {
                e.preventDefault();
                submitAccountForm($(this), 'Address saved', false);
            });
        });
    </script>
@endsection
