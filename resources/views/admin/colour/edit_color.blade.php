@extends('admin.layouts.app')
@section('title', 'Update Color')
@section('subTitle', 'Color')

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
                                    <li class="nav-item"><a class="nav-link active"
                                            href="{{ route('admin.edit_color', $colors->id) }}"> Edit Color</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.color') }}"><i
                                                class="fa fa-list-ul"></i>Color List</a></li>
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
                            <h3 class="card-title">Update Color</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="editColorForm" class="card-body" id="editColorForm">
                                <input type="hidden" name="id" value="{{ $colors->id }}">

                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Color Name</label>
                                            <input type="text" class="form-control" name="color_name" id="color_name"
                                                placeholder="Enter Color Name" value="{{ $colors->color_name }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Color Code</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="color_code" id="color_code"
                                                    placeholder="#FF5733" maxlength="7" value="{{ $colors->color_code }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text p-1">
                                                        <input type="color" id="color_wheel"
                                                            value="{{ $colors->color_code }}"
                                                            style="width:32px; height:32px; border:none; cursor:pointer; padding:0;">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-2" style="display:flex; align-items:center;">
                                                <div id="color_preview"
                                                    style="width:34px; height:34px; border-radius:5px; border:1px solid #dee2e6;
                               background:{{ $colors->color_code }}; flex-shrink:0; margin-right:8px;">
                                                </div>
                                                <small id="color_preview_label"
                                                    class="text-muted">{{ $colors->color_code }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Quick-select preset palette --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Quick Select</label>
                                            <div style="display:flex; flex-wrap:wrap;" id="color_palette">
                                                @php
                                                    $presets = [
                                                        ['name' => 'Red', 'code' => '#EF4444'],
                                                        ['name' => 'Orange', 'code' => '#F97316'],
                                                        ['name' => 'Yellow', 'code' => '#EAB308'],
                                                        ['name' => 'Green', 'code' => '#22C55E'],
                                                        ['name' => 'Teal', 'code' => '#14B8A6'],
                                                        ['name' => 'Blue', 'code' => '#3B82F6'],
                                                        ['name' => 'Indigo', 'code' => '#6366F1'],
                                                        ['name' => 'Purple', 'code' => '#A855F7'],
                                                        ['name' => 'Pink', 'code' => '#EC4899'],
                                                        ['name' => 'Rose', 'code' => '#F43F5E'],
                                                        ['name' => 'White', 'code' => '#FFFFFF'],
                                                        ['name' => 'Light Gray', 'code' => '#D1D5DB'],
                                                        ['name' => 'Gray', 'code' => '#6B7280'],
                                                        ['name' => 'Dark Gray', 'code' => '#374151'],
                                                        ['name' => 'Black', 'code' => '#111827'],
                                                        ['name' => 'Brown', 'code' => '#92400E'],
                                                        ['name' => 'Gold', 'code' => '#D97706'],
                                                        ['name' => 'Silver', 'code' => '#9CA3AF'],
                                                        ['name' => 'Navy', 'code' => '#1E3A5F'],
                                                        ['name' => 'Maroon', 'code' => '#7F1D1D'],
                                                    ];
                                                @endphp

                                                @foreach ($presets as $color)
                                                    <div class="color-swatch" data-name="{{ $color['name'] }}"
                                                        data-code="{{ $color['code'] }}"
                                                        title="{{ $color['name'] }} — {{ $color['code'] }}"
                                                        style="
                                                            width:32px; height:32px; border-radius:5px;
                                                            background:{{ $color['code'] }};
                                                            border:2px solid #e5e7eb;
                                                            cursor:pointer;
                                                            margin:0 6px 6px 0;
                                                            transition:transform .15s, border-color .15s;
                                                            {{ $color['code'] === $colors->color_code ? 'border-color:#374151; transform:scale(1.2);' : '' }}
                                                        ">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted">Click any swatch to auto-fill name &amp; code</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-md-6 col-sm-12">
                                        <label>Status</label>
                                        <select class="form-control show-tick" name="status" id="status">
                                            <option value="">-- Status --</option>
                                            <option value="1" {{ $colors->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $colors->status == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" name="color_edit_submit"
                                            id="color_editform_submit">Update</button>
                                        <button type="reset" class="btn btn-outline-secondary" name="cancel"
                                            id="color_form_cancel">Cancel</button>
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

            const nameInput = document.getElementById('color_name');
            const codeInput = document.getElementById('color_code');
            const colorWheel = document.getElementById('color_wheel');
            const preview = document.getElementById('color_preview');
            const previewLabel = document.getElementById('color_preview_label');

            function updatePreview(hex) {
                if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                    preview.style.background = hex;
                    colorWheel.value = hex;
                    previewLabel.textContent = hex.toUpperCase();
                }
            }

            // ── Init with existing saved colour on page load ──
            updatePreview(codeInput.value);

            colorWheel.addEventListener('input', function() {
                codeInput.value = this.value.toUpperCase();
                updatePreview(this.value);
            });

            codeInput.addEventListener('input', function() {
                let val = this.value.trim();
                if (!val.startsWith('#')) val = '#' + val;
                this.value = val;
                updatePreview(val);
            });

            document.querySelectorAll('.color-swatch').forEach(function(swatch) {
                swatch.addEventListener('click', function() {
                    nameInput.value = this.dataset.name;
                    codeInput.value = this.dataset.code;
                    colorWheel.value = this.dataset.code;
                    updatePreview(this.dataset.code);

                    document.querySelectorAll('.color-swatch').forEach(s => {
                        s.style.borderColor = '#e5e7eb';
                        s.style.transform = 'scale(1)';
                    });
                    this.style.borderColor = '#374151';
                    this.style.transform = 'scale(1.2)';
                });

                swatch.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.15)';
                });
                swatch.addEventListener('mouseleave', function() {
                    if (this.style.borderColor !== 'rgb(55, 65, 81)') {
                        this.style.transform = 'scale(1)';
                    }
                });
            });

        });
    </script>

@endsection
