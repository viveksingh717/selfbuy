@extends('admin.layouts.app')

@section('title', 'System Settings')
@section('subTitle', 'System Settings')

@section('style')
    <style>
        .setting-file-preview { max-height: 60px; max-width: 180px; background: #f4f6fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 4px; }
        .setting-file-current { font-size: 12px; }
    </style>
@endsection

@section('content')

    {{-- Store maintenance switch - its own forms, kept outside the settings form below. --}}
    @php $maintenance = \App\Http\Controllers\Admin\MaintenanceController::status(); @endphp
    <div class="section-body mt-3">
        <div class="container-fluid">
            <div class="card mb-0 {{ $maintenance['down'] ? 'border-warning' : '' }}">
                <div class="card-body d-md-flex align-items-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h3 class="card-title mb-1">
                            <i class="fa fa-wrench mr-1"></i> Maintenance Mode
                            @if ($maintenance['down'])
                                <span class="badge badge-warning ml-2">ON - store is offline</span>
                            @else
                                <span class="badge badge-success ml-2">OFF - store is live</span>
                            @endif
                        </h3>
                        <div class="text-muted small">
                            @if ($maintenance['down'])
                                Visitors see the "We'll be back shortly" page. The admin panel and payments keep working.
                                @if ($maintenance['preview_url'])
                                    <br>Preview the store as a visitor:
                                    <a href="{{ $maintenance['preview_url'] }}" target="_blank" rel="noopener">open private preview link</a>
                                    <span class="text-muted">(works only in your browser, don't share it)</span>
                                @endif
                            @else
                                Take the storefront offline while you update products or fix something. The admin panel stays available.
                            @endif
                        </div>
                    </div>
                    @if ($maintenance['down'])
                        <form method="POST" action="{{ route('admin.maintenance.disable') }}"
                            onsubmit="return confirm('Bring the store back online for everyone?');">
                            @csrf
                            <button type="submit" class="btn btn-success"><i class="fa fa-play mr-1"></i> Go Live</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.maintenance.enable') }}"
                            onsubmit="return confirm('Take the store offline? Visitors will see the maintenance page until you click Go Live.');">
                            @csrf
                            <button type="submit" class="btn btn-warning"><i class="fa fa-pause mr-1"></i> Enable Maintenance</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.update_settings') }}" enctype="multipart/form-data">
        @csrf

        <div class="section-body mt-3">
            <div class="container-fluid">
                <div class="d-lg-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs page-header-tab">
                        @foreach ($schema as $groupKey => $section)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active show' : '' }}" data-toggle="tab"
                                    href="#tab-{{ $groupKey }}">{{ $section['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <button type="submit" class="btn btn-primary d-none d-lg-inline-block">
                        <i class="fa fa-save"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="section-body mt-3">
            <div class="container-fluid">
                @include('partials._message')

                <div class="tab-content">
                    @foreach ($schema as $groupKey => $section)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $groupKey }}">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $section['label'] }} Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row clearfix">
                                        @foreach ($section['fields'] as $key => $meta)
                                            @php $value = $settings[$key] ?? ($meta['default'] ?? ''); @endphp

                                            <div class="{{ $meta['type'] === 'textarea' ? 'col-md-12' : 'col-md-6' }} col-sm-12">
                                                <div class="form-group">
                                                    <label>{{ $meta['label'] }}</label>

                                                    @switch($meta['type'])
                                                        @case('textarea')
                                                            <textarea class="form-control" name="{{ $key }}" rows="3">{{ old($key, $value) }}</textarea>
                                                            @break

                                                        @case('boolean')
                                                            <select class="form-control show-tick" name="{{ $key }}">
                                                                <option value="1" {{ (string) old($key, $value) === '1' ? 'selected' : '' }}>Enabled</option>
                                                                <option value="0" {{ (string) old($key, $value) !== '1' ? 'selected' : '' }}>Disabled</option>
                                                            </select>
                                                            @break

                                                        @case('select')
                                                            <select class="form-control show-tick" name="{{ $key }}">
                                                                @foreach ($meta['options'] as $optVal => $optLabel)
                                                                    <option value="{{ $optVal }}" {{ (string) old($key, $value) === (string) $optVal ? 'selected' : '' }}>
                                                                        {{ $optLabel }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @break

                                                        @case('number')
                                                            <input type="number" class="form-control" name="{{ $key }}" value="{{ old($key, $value) }}">
                                                            @break

                                                        @case('file')
                                                            @if (!empty($value))
                                                                <div class="setting-file-current mb-2">
                                                                    @php $isImg = \Illuminate\Support\Str::endsWith(strtolower($value), ['.jpg','.jpeg','.png','.gif','.webp','.svg']); @endphp
                                                                    @if ($isImg)
                                                                        <img src="{{ asset('storage/' . $value) }}" class="setting-file-preview" alt="{{ $key }}">
                                                                    @else
                                                                        <a href="{{ asset('storage/' . $value) }}" target="_blank" rel="noopener">{{ basename($value) }}</a>
                                                                    @endif
                                                                    <label class="ml-2">
                                                                        <input type="checkbox" name="remove_file[]" value="{{ $key }}"> remove
                                                                    </label>
                                                                </div>
                                                            @endif
                                                            <input type="file" class="form-control-file" name="{{ $key }}"
                                                                accept="{{ $meta['accept'] ?? '' }}">
                                                            <small class="text-muted d-block">Leave empty to keep the current file. Max 4 MB.</small>
                                                            @break

                                                        @default
                                                            <input type="text" class="form-control" name="{{ $key }}" value="{{ old($key, $value) }}">
                                                    @endswitch

                                                    @error($key)
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script')
@endsection
