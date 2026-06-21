@extends('admin.layouts.app')
@section('title', 'Add Coupon')
@section('subTitle', 'Coupon')

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-md-flex justify-content-between mb-2">
                                <ul class="nav nav-tabs b-none">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="{{ route('admin.create_coupon') }}">Add Coupon</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.coupon') }}">
                                            <i class="fa fa-list-ul"></i> Coupon List
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add Coupon</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="couponForm" class="card-body" id="couponForm">

                                {{-- ── Section: Basic Info ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Basic Info
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Coupon Title</label>
                                            <input type="text" class="form-control" name="coupon_name" id="coupon_name"
                                                placeholder="e.g. Summer Sale 20%">
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Coupon Code</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="coupon_code"
                                                    id="coupon_code" placeholder="e.g. SUMMER20"
                                                    style="text-transform:uppercase;">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="generateCodeBtn">
                                                        <i class="fa fa-refresh"></i> Generate
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Customers enter this at checkout</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control show-tick" name="status" id="status">
                                                <option value="">-- Status --</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── Section: Discount Type ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Discount Type
                                </h6>
                                <input type="hidden" name="discount_type" id="discount_type" value="percentage">
                                <div class="row clearfix mb-3">
                                    <div class="col-12">
                                        <div style="display:flex; flex-wrap:wrap; gap:10px;">

                                            @php
                                                $discountTypes = [
                                                    [
                                                        'value' => 'percentage',
                                                        'label' => 'Percentage',
                                                        'icon' => 'fa-percent',
                                                    ],
                                                    [
                                                        'value' => 'fixed_cart',
                                                        'label' => 'Fixed Amount',
                                                        'icon' => 'fa-inr',
                                                    ],
                                                    [
                                                        'value' => 'free_shipping',
                                                        'label' => 'Free Shipping',
                                                        'icon' => 'fa-truck',
                                                    ],
                                                    [
                                                        'value' => 'buy_x_get_y',
                                                        'label' => 'Buy X Get Y',
                                                        'icon' => 'fa-gift',
                                                    ],
                                                ];
                                            @endphp

                                            @foreach ($discountTypes as $type)
                                                <div class="discount-type-card {{ $loop->first ? 'active' : '' }}"
                                                    data-type="{{ $type['value'] }}"
                                                    style="
                                                        min-width:120px; padding:12px 16px;
                                                        border:1px solid {{ $loop->first ? '#4e73df' : '#dee2e6' }};
                                                        border-radius:6px; cursor:pointer;
                                                        text-align:center;
                                                        background:{{ $loop->first ? '#eef2ff' : '#fff' }};
                                                        transition:all .15s;
                                                    ">
                                                    <div
                                                        style="font-size:20px; margin-bottom:6px;
                                                                color:{{ $loop->first ? '#4e73df' : '#6c757d' }};">
                                                        <i class="fa {{ $type['icon'] }}"></i>
                                                    </div>
                                                    <div
                                                        style="font-size:12px; font-weight:500;
                                                                color:{{ $loop->first ? '#4e73df' : '#6c757d' }};">
                                                        {{ $type['label'] }}
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── Section: Discount Value ── --}}
                                <div id="discountValueSection">
                                    <h6 class="text-muted font-weight-bold mb-3"
                                        style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                        Discount Value
                                    </h6>
                                    <div class="row clearfix">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>
                                                    Discount Value
                                                    <span class="badge badge-info ml-1" id="discountBadge">%</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="discount_value"
                                                        id="discount_value" placeholder="e.g. 20" min="0">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="discountAddon">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>Minimum Order Amount</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">₹</span>
                                                    </div>
                                                    <input type="number" class="form-control" name="min_order_amount"
                                                        id="min_order_amount" placeholder="e.g. 500" min="0">
                                                </div>
                                                <small class="text-muted">Leave 0 for no minimum</small>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>Maximum Discount Cap</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">₹</span>
                                                    </div>
                                                    <input type="number" class="form-control" name="max_discount_amount"
                                                        id="max_discount_amount" placeholder="e.g. 200" min="0">
                                                </div>
                                                <small class="text-muted">Max cap (for % type only)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="mt-1 mb-3">
                                </div>

                                {{-- ── Section: Validity & Usage ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Validity & Usage
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" name="start_date" id="start_date"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Expiry Date</label>
                                            <input type="date" class="form-control" name="expiry_date"
                                                id="expiry_date">
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Total Usage Limit</label>
                                            <input type="number" class="form-control" name="usage_limit"
                                                id="usage_limit" placeholder="e.g. 100" min="1">
                                            <small class="text-muted">Total times this coupon can be used</small>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Per User Limit</label>
                                            <input type="number" class="form-control" name="per_user_limit"
                                                id="per_user_limit" placeholder="e.g. 1" min="1">
                                            <small class="text-muted">Times one user can use it</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── Section: Optional ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Optional
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-8 col-sm-12">
                                        <div class="form-group">
                                            <label>Description <small class="text-muted">(internal note)</small></label>
                                            <textarea class="form-control" name="description" id="description" rows="2"
                                                placeholder="e.g. Diwali sale coupon for first-time buyers"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Applicable To</label>
                                            <select class="form-control" name="applicable_to" id="applicable_to">
                                                <option value="all">All Products</option>
                                                <option value="category">Specific Category</option>
                                                <option value="product">Specific Product</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary"
                                            id="coupon_form_submit">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary"
                                            id="coupon_form_cancel">Cancel</button>
                                    </div>
                                </div>

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
        document.addEventListener('DOMContentLoaded', function() {

            // ── Discount type card toggle ──────────────────────────
            document.querySelectorAll('.discount-type-card').forEach(function(card) {
                card.addEventListener('click', function() {

                    document.querySelectorAll('.discount-type-card').forEach(function(c) {
                        c.style.borderColor = '#dee2e6';
                        c.style.background = '#fff';
                        c.querySelector('div:first-child').style.color = '#6c757d';
                        c.querySelector('div:last-child').style.color = '#6c757d';
                    });

                    card.style.borderColor = '#4e73df';
                    card.style.background = '#eef2ff';
                    card.querySelector('div:first-child').style.color = '#4e73df';
                    card.querySelector('div:last-child').style.color = '#4e73df';

                    const type = card.dataset.type;
                    document.getElementById('discount_type').value = type;

                    // Hide discount value section for free shipping
                    const section = document.getElementById('discountValueSection');
                    section.style.display = type === 'free_shipping' ? 'none' : 'block';

                    // Swap % / ₹ addon
                    const isPercent = type === 'percentage';
                    document.getElementById('discountAddon').textContent = isPercent ? '%' : '₹';
                    document.getElementById('discountBadge').textContent = isPercent ? '%' : '₹';
                });
            });

            // ── Auto-generate coupon code ──────────────────────────
            document.getElementById('generateCodeBtn').addEventListener('click', function() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let code = '';
                for (let i = 0; i < 8; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                document.getElementById('coupon_code').value = code;
            });

            // ── Force coupon code to uppercase while typing ────────
            document.getElementById('coupon_code').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // ── Expiry must be after start date ───────────────────
            document.getElementById('start_date').addEventListener('change', function() {
                document.getElementById('expiry_date').min = this.value;
            });

        });
    </script>
@endsection
