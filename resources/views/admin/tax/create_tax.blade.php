@extends('admin.layouts.app')
@section('title', 'Add Tax')
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
                                        <a class="nav-link active" href="{{ route('admin.create_tax') }}">Add Tax</a>
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
                            <h3 class="card-title">Add Tax</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="taxForm" class="card-body" id="taxForm">

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
                                                placeholder="e.g. GST, VAT, IGST" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Tax Alias / Short Code</label>
                                            <input type="text" class="form-control" name="tax_alias" id="tax_alias"
                                                placeholder="e.g. GST18, VAT5" style="text-transform:uppercase;">
                                            <small class="text-muted">Used as a short label on invoices</small>
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

                                {{-- ── Section: Tax Type ── --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Tax Type
                                </h6>
                                <input type="hidden" name="tax_type" id="tax_type" value="percentage">
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
                                                <div class="tax-type-card {{ $loop->first ? 'active' : '' }}"
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
                                                <span class="badge badge-info ml-1" id="rateBadge">%</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="tax_rate" id="tax_rate"
                                                    placeholder="e.g. 18" min="0" max="100" step="0.01"
                                                    required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="rateAddon">%</span>
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
                                                    <div class="tax-slab" data-rate="{{ $slab['rate'] }}"
                                                        style="
                                                            display:inline-flex; align-items:center; justify-content:center;
                                                            padding:4px 14px; border-radius:20px;
                                                            font-size:13px; font-weight:500; cursor:pointer;
                                                            border:1px solid #dee2e6; background:#f8f9fa;
                                                            color:#495057; transition:all .15s;
                                                        ">
                                                        {{ $slab['label'] }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted">Click any slab to auto-fill rate</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Live preview ── --}}
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
                                                <option value="all">All Products</option>
                                                <option value="category">Specific Category</option>
                                                <option value="product">Specific Product</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Tax Region</label>
                                            <select class="form-control" name="tax_region" id="tax_region">
                                                <option value="all">All Regions</option>
                                                <option value="domestic">Domestic Only</option>
                                                <option value="international">International Only</option>
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
                                                placeholder="e.g. 1" min="1" value="1">
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
                                                placeholder="e.g. GST 18% applicable on electronics as per government regulations">
                                            </textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary"
                                            id="tax_form_submit">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary"
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
            let activeSlab = null;

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
                // clear active slab if user types manually
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
                    // percentage & compound both show same for preview
                    tax = base * rate / 100;
                    total = base + tax;
                }

                document.getElementById('taxAmount').textContent = '₹' + tax.toFixed(2);
                document.getElementById('totalAmount').textContent = '₹' + total.toFixed(2);
            }

            // ── Force alias to uppercase ──────────────────────────
            document.getElementById('tax_alias').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // ── Reset clears active slab ──────────────────────────
            document.getElementById('tax_form_cancel').addEventListener('click', function() {
                if (activeSlab) {
                    activeSlab.style.background = '#f8f9fa';
                    activeSlab.style.color = '#495057';
                    activeSlab.style.borderColor = '#dee2e6';
                    activeSlab = null;
                }
                document.getElementById('taxAmount').textContent = '₹0.00';
                document.getElementById('totalAmount').textContent = '₹1,000.00';
            });

        });
    </script>
@endsection
