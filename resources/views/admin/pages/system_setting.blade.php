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
