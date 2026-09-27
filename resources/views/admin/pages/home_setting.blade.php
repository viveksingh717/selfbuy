@extends('admin.layouts.app')

@section('title', 'Home Settings')
@section('subTitle', 'Home Settings')

@section('style')
    <style>
        .setting-file-preview { max-height: 60px; max-width: 180px; background: #f4f6fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 4px; }
        .setting-file-current { font-size: 12px; }
        .home-slide-card .card-header { background: #f8f9fa; }
    </style>
@endsection

@section('content')

    <form method="POST" action="{{ route('admin.update_home_settings') }}" enctype="multipart/form-data">
        @csrf

        <div class="section-body mt-3">
            <div class="container-fluid">
                <div class="d-lg-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs page-header-tab">
                        <li class="nav-item">
                            <a class="nav-link active show" data-toggle="tab" href="#tab-carousel">Carousel</a>
                        </li>
                        @foreach ($schema as $groupKey => $section)
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab"
                                    href="#tab-{{ $groupKey }}">{{ $section['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <button type="submit" class="btn btn-primary d-none d-lg-inline-block">
                        <i class="fa fa-save"></i> Save Home Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="section-body mt-3">
            <div class="container-fluid">
                @include('partials._message')

                <div class="tab-content">
                    {{-- Carousel: any number of slides (home_slides table) --}}
                    <div class="tab-pane fade show active" id="tab-carousel">
                        @if ($errors->has('slides.*') || $errors->has('new_slides.*'))
                            <div class="alert alert-danger">
                                @foreach (array_unique(array_merge($errors->get('slides.*'), $errors->get('new_slides.*')), SORT_REGULAR) as $messages)
                                    @foreach ((array) $messages as $message)
                                        <div>{{ $message }}</div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif

                        <div id="home-slides">
                            @forelse ($slides as $slide)
                                @include('admin.pages.home_slide_card', ['slide' => $slide, 'name' => "slides[{$slide->id}]", 'old' => "slides.{$slide->id}"])
                            @empty
                                <p class="text-muted js-no-slides">No slides yet - add one below.</p>
                            @endforelse
                        </div>

                        <button type="button" class="btn btn-outline-primary mb-3" id="add-slide">
                            <i class="fa fa-plus"></i> Add Slide
                        </button>
                    </div>

                    @foreach ($schema as $groupKey => $section)
                        <div class="tab-pane fade" id="tab-{{ $groupKey }}">
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
                                                            @elseif (!empty($meta['placeholder']))
                                                                <div class="setting-file-current mb-2">
                                                                    <img src="{{ asset($meta['placeholder']) }}" class="setting-file-preview" alt="{{ $key }}">
                                                                    <span class="text-muted ml-2">default image</span>
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
                        <i class="fa fa-save"></i> Save Home Settings
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script')
    {{-- Blank slide card, cloned by "Add Slide". __INDEX__ is swapped for a unique number. --}}
    <template id="slide-template">
        @include('admin.pages.home_slide_card', ['slide' => null, 'name' => 'new_slides[__INDEX__]', 'old' => null])
    </template>

    <script>
        (function () {
            var list = document.getElementById('home-slides');
            var template = document.getElementById('slide-template').innerHTML;
            var nextIndex = 0;

            document.getElementById('add-slide').addEventListener('click', function () {
                var empty = list.querySelector('.js-no-slides');
                if (empty) empty.remove();

                var wrapper = document.createElement('div');
                wrapper.innerHTML = template.replace(/__INDEX__/g, nextIndex++);
                var card = wrapper.firstElementChild;
                list.appendChild(card);
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });

            list.addEventListener('click', function (e) {
                var btn = e.target.closest('.js-remove-slide');
                if (btn) btn.closest('.home-slide-card').remove();
            });
        })();
    </script>
@endsection
