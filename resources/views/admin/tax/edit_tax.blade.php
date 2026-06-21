@extends('admin.layouts.app')
@section('title', 'Update Tax')
@section('subTitle', 'Tax')

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
                                        <a class="nav-link active" href="{{ route('admin.edit_tax', $tax->id) }}">
                                            Edit Tax
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.tax') }}">
                                            <i class="fa fa-list-ul"></i> Tax List
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
                            <h3 class="card-title">Update Tax</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="editTaxForm" class="card-body" id="editTaxForm">
                                <input type="hidden" name="id" value="{{ $tax->id }}">

                                {{-- ── Section: Basic Info ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Basic Info
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Tax Name</label>
                                            <input type="text" class="form-control" name="tax_name" id="tax_name"
                                                placeholder="e.g. GST, VAT, IGST" value="{{ $tax->tax_name }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Tax Alias / Short Code</label>
                                            <input type="text" class="form-control" name="tax_alias" id="tax_alias"
                                                placeholder="e.g. GST18, VAT5" style="text-transform:uppercase;"
                                                value="{{ $tax->tax_alias }}">
                                            <small class="text-muted">Used as a short label on invoices</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control show-tick" name="status" id="status">
                                                <option value="">-- Status --</option>
                                                <option value="1" {{ $tax->status == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ $tax->status == 0 ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── Section: Tax Type ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Tax Type
                                </h6>
                                <input type="hidden" name="tax_type" id="tax_type" value="{{ $tax->tax_type }}">
                                <div class="row clearfix mb-3">
                                    <div class="col-12">
                                        <div style="display:flex; flex-wrap:wrap; gap:10px;">

                                            @php
                                                $taxTypes = [
                                                    [
                                                        'value' => 'percentage',
                                                        'label' => 'Percentage (%)',
                                                        'icon' => 'fa-percent',
                                                    ],
                                                    ['value' => 'fixed', 'label' => 'Fixed (₹)', 'icon' => 'fa-inr'],
                                                    [
                                                        'value' => 'compound',
                                                        'label' => 'Compound',
                                                        'icon' => 'fa-plus-circle',
                                                    ],
                                                    [
                                                        'value' => 'inclusive',
                                                        'label' => 'Price Inclusive',
                                                        'icon' => 'fa-tag',
                                                    ],
                                                ];
                                            @endphp

                                            @foreach ($taxTypes as $type)
                                                @php $isActive = $tax->tax_type === $type['value']; @endphp
                                                <div class="tax-type-card {{ $isActive ? 'active' : '' }}"
                                                    data-type="{{ $type['value'] }}"
                                                    style="
                                                        min-width:120px; padding:12px 16px;
                                                        border:1px solid {{ $isActive ? '#4e73df' : '#dee2e6' }};
                                                        border-radius:6px; cursor:pointer;
                                                        text-align:center;
                                                        background:{{ $isActive ? '#eef2ff' : '#fff' }};
                                                        transition:all .15s;
                                                    ">
                                                    <div
                                                        style="font-size:20px; margin-bottom:6px;
                                                                color:{{ $isActive ? '#4e73df' : '#6c757d' }};">
                                                        <i class="fa {{ $type['icon'] }}"></i>
                                                    </div>
                                                    <div
                                                        style="font-size:12px; font-weight:500;
                                                                color:{{ $isActive ? '#4e73df' : '#6c757d' }};">
                                                        {{ $type['label'] }}
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── Section: Tax Rate ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Tax Rate
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                Tax Rate
                                                <span class="badge badge-info ml-1" id="rateBadge">
                                                    {{ $tax->tax_type === 'fixed' ? '₹' : '%' }}
                                                </span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="tax_rate" id="tax_rate"
                                                    placeholder="e.g. 18" min="0" max="100" step="0.01"
                                                    required value="{{ $tax->tax_rate }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="rateAddon">
                                                        {{ $tax->tax_type === 'fixed' ? '₹' : '%' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-sm-12">
                                        <div class="form-group">
                                            <label>Quick Select — GST / Common Slabs</label>
                                            <div style="display:flex; flex-wrap:wrap; gap:6px;" id="slabWrap">

                                                @php
                                                    $slabs = [
                                                        ['label' => '0%', 'rate' => 0],
                                                        ['label' => '0.1%', 'rate' => 0.1],
                                                        ['label' => '0.25%', 'rate' => 0.25],
                                                        ['label' => '1%', 'rate' => 1],
                                                        ['label' => '1.5%', 'rate' => 1.5],
                                                        ['label' => '3%', 'rate' => 3],
                                                        ['label' => '5%', 'rate' => 5],
                                                        ['label' => '7.5%', 'rate' => 7.5],
                                                        ['label' => '12%', 'rate' => 12],
                                                        ['label' => '18%', 'rate' => 18],
                                                        ['label' => '28%', 'rate' => 28],
                                                    ];
                                                @endphp

                                                @foreach ($slabs as $slab)
                                                    @php $slabActive = (float)$tax->tax_rate === (float)$slab['rate']; @endphp
                                                    <div class="tax-slab {{ $slabActive ? 'slab-active' : '' }}"
                                                        data-rate="{{ $slab['rate'] }}"
                                                        style="
                                                            display:inline-flex; align-items:center; justify-content:center;
                                                            padding:4px 14px; border-radius:20px;
                                                            font-size:13px; font-weight:500; cursor:pointer;
                                                            border:1px solid {{ $slabActive ? '#4e73df' : '#dee2e6' }};
                                                            background:{{ $slabActive ? '#4e73df' : '#f8f9fa' }};
                                                            color:{{ $slabActive ? '#fff' : '#495057' }};
                                                            transition:all .15s;
                                                        ">
                                                        {{ $slab['label'] }}
                                                    </div>
                                                @endforeach

                                            </div>
                                            <small class="text-muted">Click any slab to auto-fill rate</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Live preview ── --}}
                                <div class="row clearfix">
                                    <div class="col-12">
                                        <div id="livePreview"
                                            style="display:flex; align-items:center; gap:10px;
                                                   padding:10px 14px; background:#f8f9fa;
                                                   border:1px solid #dee2e6; border-radius:6px;
                                                   font-size:13px; color:#6c757d; margin-bottom:1rem;">
                                            <i class="fa fa-calculator"></i>
                                            On a product priced at ₹1,000 &nbsp;→&nbsp;
                                            tax = <strong id="taxAmount"
                                                style="color:#212529; font-size:15px;">₹0.00</strong>
                                            &nbsp;|&nbsp;
                                            total = <strong id="totalAmount"
                                                style="color:#212529; font-size:15px;">₹1,000.00</strong>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ── Section: Applicability ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Applicability
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Applicable To</label>
                                            <select class="form-control" name="applicable_to" id="applicable_to">
                                                <option value="all"
                                                    {{ $tax->applicable_to === 'all' ? 'selected' : '' }}>All Products
                                                </option>
                                                <option value="category"
                                                    {{ $tax->applicable_to === 'category' ? 'selected' : '' }}>Specific
                                                    Category</option>
                                                <option value="product"
                                                    {{ $tax->applicable_to === 'product' ? 'selected' : '' }}>Specific
                                                    Product</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Tax Region</label>
                                            <select class="form-control" name="tax_region" id="tax_region">
                                                <option value="all"
                                                    {{ $tax->tax_region === 'all' ? 'selected' : '' }}>All
                                                    Regions</option>
                                                <option value="domestic"
                                                    {{ $tax->tax_region === 'domestic' ? 'selected' : '' }}>Domestic
                                                    Only</option>
                                                <option value="international"
                                                    {{ $tax->tax_region === 'international' ? 'selected' : '' }}>
                                                    International Only</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                Priority
                                                <small class="text-muted">(for stacking)</small>
                                            </label>
                                            <input type="number" class="form-control" name="priority" id="priority"
                                                placeholder="e.g. 1" min="1" value="{{ $tax->priority }}">
                                            <small class="text-muted">Lower number = applied first</small>
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
                                                placeholder="e.g. GST 18% applicable on electronics as per government regulations">{{ $tax->description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary"
                                            id="tax_editform_submit">Update</button>
                                        <button type="button" class="btn btn-outline-secondary"
                                            id="tax_form_cancel">Cancel</button>
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

            // ── Tax type card toggle ───────────────────────────────
            document.querySelectorAll('.tax-type-card').forEach(function(card) {
                card.addEventListener('click', function() {

                    document.querySelectorAll('.tax-type-card').forEach(function(c) {
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
                    document.getElementById('tax_type').value = type;

                    const isFixed = type === 'fixed';
                    document.getElementById('rateAddon').textContent = isFixed ? '₹' : '%';
                    document.getElementById('rateBadge').textContent = isFixed ? '₹' : '%';

                    updatePreview();
                });
            });

            // ── Slab quick select ─────────────────────────────────
            let activeSlab = document.querySelector('.tax-slab.slab-active') || null;

            document.querySelectorAll('.tax-slab').forEach(function(slab) {
                slab.addEventListener('click', function() {
                    if (activeSlab) {
                        activeSlab.style.background = '#f8f9fa';
                        activeSlab.style.color = '#495057';
                        activeSlab.style.borderColor = '#dee2e6';
                    }
                    slab.style.background = '#4e73df';
                    slab.style.color = '#fff';
                    slab.style.borderColor = '#4e73df';
                    activeSlab = slab;

                    document.getElementById('tax_rate').value = slab.dataset.rate;
                    updatePreview();
                });

                slab.addEventListener('mouseenter', function() {
                    if (activeSlab !== slab) {
                        slab.style.background = '#e9ecef';
                        slab.style.borderColor = '#6c757d';
                    }
                });
                slab.addEventListener('mouseleave', function() {
                    if (activeSlab !== slab) {
                        slab.style.background = '#f8f9fa';
                        slab.style.borderColor = '#dee2e6';
                    }
                });
            });

            // ── Live preview ──────────────────────────────────────
            document.getElementById('tax_rate').addEventListener('input', function() {
                if (activeSlab) {
                    activeSlab.style.background = '#f8f9fa';
                    activeSlab.style.color = '#495057';
                    activeSlab.style.borderColor = '#dee2e6';
                    activeSlab = null;
                }
                updatePreview();
            });

            function updatePreview() {
                const rate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const type = document.getElementById('tax_type').value;
                const base = 1000;
                let tax, total;

                if (type === 'fixed') {
                    tax = rate;
                    total = base + rate;
                } else if (type === 'inclusive') {
                    tax = base - (base * 100 / (100 + rate));
                    total = base;
                } else {
                    tax = base * rate / 100;
                    total = base + tax;
                }

                document.getElementById('taxAmount').textContent = '₹' + tax.toFixed(2);
                document.getElementById('totalAmount').textContent = '₹' + total.toFixed(2);
            }

            // ── Init preview on page load with saved values ───────
            updatePreview();

            // ── Force alias to uppercase ──────────────────────────
            document.getElementById('tax_alias').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // ── Cancel button ─────────────────────────────────────
            document.getElementById('tax_form_cancel').addEventListener('click', function() {
                window.location.href = '{{ route('admin.tax') }}';
            });

            // ── AJAX submit ───────────────────────────────────────
            $('#editTaxForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('input[name="id"]').val();
                const btn = $('#tax_editform_submit');

                btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: '/admin/update_tax/' + id,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            setTimeout(() => window.location.href = '{{ route('admin.tax') }}',
                                1200);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(err => toastr.error(err[0]));
                        } else {
                            toastr.error('Something went wrong.');
                        }
                        btn.prop('disabled', false).text('Update');
                    }
                });
            });

        });
    </script>
@endsection
