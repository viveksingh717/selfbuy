@extends('admin.layouts.app')
@section('title', 'Update Size')
@section('subTitle', 'Size')

@section('style')
@endsection

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
                                        <a class="nav-link active" href="{{ route('admin.edit_size', $sizes->id) }}">
                                            Edit Size
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.size') }}">
                                            <i class="fa fa-list-ul"></i> Size List
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
                            <h3 class="card-title">Update Size</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="editSizeForm" class="card-body" id="editSizeForm">
                                <input type="hidden" name="id" value="{{ $sizes->id }}">

                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Size Name</label>
                                            <input type="text" class="form-control" name="size_name" id="size_name"
                                                placeholder="Enter Size Name" value="{{ $sizes->size_name }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Size Code</label>
                                            <input type="text" class="form-control" name="size_code" id="size_code"
                                                placeholder="e.g. XL" value="{{ $sizes->size_code }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Select Size Chips --}}
                                <div class="row clearfix">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Quick Select</label>

                                            @php
                                                $sizeGroups = [
                                                    'Clothing' => [
                                                        ['name' => 'Extra Small', 'code' => 'XS'],
                                                        ['name' => 'Small', 'code' => 'S'],
                                                        ['name' => 'Medium', 'code' => 'M'],
                                                        ['name' => 'Large', 'code' => 'L'],
                                                        ['name' => 'Extra Large', 'code' => 'XL'],
                                                        ['name' => 'Double XL', 'code' => 'XXL'],
                                                        ['name' => 'Triple XL', 'code' => '3XL'],
                                                        ['name' => '4XL', 'code' => '4XL'],
                                                        ['name' => '5XL', 'code' => '5XL'],
                                                    ],
                                                    'Numeric (EU)' => [
                                                        ['name' => '28', 'code' => '28'],
                                                        ['name' => '30', 'code' => '30'],
                                                        ['name' => '32', 'code' => '32'],
                                                        ['name' => '34', 'code' => '34'],
                                                        ['name' => '36', 'code' => '36'],
                                                        ['name' => '38', 'code' => '38'],
                                                        ['name' => '40', 'code' => '40'],
                                                        ['name' => '42', 'code' => '42'],
                                                        ['name' => '44', 'code' => '44'],
                                                        ['name' => '46', 'code' => '46'],
                                                        ['name' => '48', 'code' => '48'],
                                                    ],
                                                    'Numeric (US / Inch)' => [
                                                        ['name' => '6 Inch', 'code' => '6"'],
                                                        ['name' => '7 Inch', 'code' => '7"'],
                                                        ['name' => '8 Inch', 'code' => '8"'],
                                                        ['name' => '9 Inch', 'code' => '9"'],
                                                        ['name' => '10 Inch', 'code' => '10"'],
                                                        ['name' => '11 Inch', 'code' => '11"'],
                                                        ['name' => '12 Inch', 'code' => '12"'],
                                                    ],
                                                    'Kids' => [
                                                        ['name' => '0-3 Months', 'code' => '0-3M'],
                                                        ['name' => '3-6 Months', 'code' => '3-6M'],
                                                        ['name' => '6-12 Months', 'code' => '6-12M'],
                                                        ['name' => '1-2 Years', 'code' => '1-2Y'],
                                                        ['name' => '2-3 Years', 'code' => '2-3Y'],
                                                        ['name' => '3-4 Years', 'code' => '3-4Y'],
                                                        ['name' => '4-5 Years', 'code' => '4-5Y'],
                                                        ['name' => '6-7 Years', 'code' => '6-7Y'],
                                                        ['name' => '8-9 Years', 'code' => '8-9Y'],
                                                        ['name' => '10-11 Years', 'code' => '10-11Y'],
                                                    ],
                                                    'Free Size' => [
                                                        ['name' => 'Free Size', 'code' => 'FREE'],
                                                        ['name' => 'One Size', 'code' => 'OS'],
                                                        ['name' => 'Adjustable', 'code' => 'ADJ'],
                                                    ],
                                                ];
                                            @endphp

                                            @foreach ($sizeGroups as $groupName => $sizess)
                                                <p class="text-muted mb-1 mt-2"
                                                    style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">
                                                    {{ $groupName }}
                                                </p>
                                                <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:4px;">
                                                    @foreach ($sizess as $size)
                                                        <div class="size-chip" data-name="{{ $size['name'] }}"
                                                            data-code="{{ $size['code'] }}"
                                                            title="{{ $size['name'] }} ({{ $size['code'] }})"
                                                            style="
                                                                display:inline-flex; align-items:center; justify-content:center;
                                                                padding:4px 14px; border-radius:20px;
                                                                font-size:13px; font-weight:500;
                                                                cursor:pointer;
                                                                border:1px solid {{ $size['code'] === $sizes->size_code ? '#4e73df' : '#dee2e6' }};
                                                                background:{{ $size['code'] === $sizes->size_code ? '#4e73df' : '#f8f9fa' }};
                                                                color:{{ $size['code'] === $sizes->size_code ? '#fff' : '#495057' }};
                                                                transition:all .15s;
                                                            ">
                                                            {{ $size['code'] }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach

                                            <small class="text-muted">Click any chip to auto-fill name &amp; code. Click
                                                again to deselect.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <label>Status</label>
                                        <select class="form-control show-tick" name="status" id="status">
                                            <option value="">-- Status --</option>
                                            <option value="1" {{ $sizes->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $sizes->status == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" name="size_edit_submit"
                                            id="size_editform_submit">Update</button>
                                        <button type="reset" class="btn btn-outline-secondary" name="cancel"
                                            id="size_form_cancel">Cancel</button>
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

            const nameInput = document.getElementById('size_name');
            const codeInput = document.getElementById('size_code');
            let activeChip = null;

            // ── Init: highlight the chip matching saved size code on page load ──
            document.querySelectorAll('.size-chip').forEach(function(chip) {
                if (chip.dataset.code === codeInput.value.trim()) {
                    activeChip = chip;
                }
            });

            document.querySelectorAll('.size-chip').forEach(function(chip) {

                chip.addEventListener('click', function() {
                    if (activeChip === chip) {
                        // deselect
                        chip.style.background = '#f8f9fa';
                        chip.style.color = '#495057';
                        chip.style.borderColor = '#dee2e6';
                        nameInput.value = '';
                        codeInput.value = '';
                        activeChip = null;
                    } else {
                        // deselect previous
                        if (activeChip) {
                            activeChip.style.background = '#f8f9fa';
                            activeChip.style.color = '#495057';
                            activeChip.style.borderColor = '#dee2e6';
                        }
                        // select this
                        chip.style.background = '#4e73df';
                        chip.style.color = '#fff';
                        chip.style.borderColor = '#4e73df';
                        nameInput.value = chip.dataset.name;
                        codeInput.value = chip.dataset.code;
                        activeChip = chip;
                    }
                });

                chip.addEventListener('mouseenter', function() {
                    if (activeChip !== chip) {
                        chip.style.borderColor = '#6c757d';
                        chip.style.background = '#e9ecef';
                    }
                });

                chip.addEventListener('mouseleave', function() {
                    if (activeChip !== chip) {
                        chip.style.borderColor = '#dee2e6';
                        chip.style.background = '#f8f9fa';
                    }
                });
            });

            // ── Reset button clears active chip highlight ──
            document.getElementById('size_form_cancel').addEventListener('click', function() {
                if (activeChip) {
                    activeChip.style.background = '#f8f9fa';
                    activeChip.style.color = '#495057';
                    activeChip.style.borderColor = '#dee2e6';
                    activeChip = null;
                }
            });

        });
    </script>
@endsection
