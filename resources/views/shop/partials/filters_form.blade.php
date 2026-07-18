<form method="GET" action="{{ url()->current() }}">
    @if (request('q'))
        <input type="hidden" name="q" value="{{ request('q') }}">
    @endif

    @foreach ($selectedColorIds as $colorId)
        <input type="hidden" name="color_id[]" value="{{ $colorId }}">
    @endforeach

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-2" role="button" aria-expanded="true"
                aria-controls="widget-2">
                Size
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-2">
            <div class="widget-body">
                <div class="filter-items">
                    @forelse ($sizes as $size)
                        <div class="filter-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                    id="size-{{ $size->id }}" name="size_id[]"
                                    value="{{ $size->id }}"
                                    {{ in_array($size->id, $selectedSizeIds) ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="size-{{ $size->id }}">{{ $size->size_code }}</label>
                            </div><!-- End .custom-checkbox -->
                        </div><!-- End .filter-item -->
                    @empty
                        <p class="mb-0">No sizes available.</p>
                    @endforelse
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-3" role="button" aria-expanded="true"
                aria-controls="widget-3">
                Colour
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-3">
            <div class="widget-body">
                <div class="filter-colors">
                    @forelse ($colors as $color)
                        @php
                            $isColorSelected = in_array($color->id, $selectedColorIds);
                            $toggledColorIds = $isColorSelected
                                ? array_values(array_diff($selectedColorIds, [$color->id]))
                                : array_merge($selectedColorIds, [$color->id]);
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['color_id' => $toggledColorIds, 'page' => null]) }}"
                            class="{{ $isColorSelected ? 'selected' : '' }}"
                            style="background: {{ $color->color_code }};">
                            <span class="sr-only">{{ $color->color_name }}</span>
                        </a>
                    @empty
                        <p class="mb-0">No colours available.</p>
                    @endforelse
                </div><!-- End .filter-colors -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-4" role="button" aria-expanded="true"
                aria-controls="widget-4">
                Brand
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-4">
            <div class="widget-body">
                <div class="filter-items">
                    @forelse ($brands as $brand)
                        <div class="filter-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                    id="brand-{{ $brand->id }}" name="brand_id[]"
                                    value="{{ $brand->id }}"
                                    {{ in_array($brand->id, $selectedBrandIds) ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                    for="brand-{{ $brand->id }}">{{ $brand->brand_name }}</label>
                            </div><!-- End .custom-checkbox -->
                        </div><!-- End .filter-item -->
                    @empty
                        <p class="mb-0">No brands available.</p>
                    @endforelse
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true"
                aria-controls="widget-5">
                Price
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-5">
            <div class="widget-body">
                <div class="filter-price">
                    <div class="filter-price-text">
                        Price Range:
                        <span id="filter-price-range"></span>
                    </div><!-- End .filter-price-text -->

                    <div id="price-slider"></div><!-- End #price-slider -->

                    <input type="hidden" name="min_price" id="min_price"
                        value="{{ $minPrice ?: 10 }}">
                    <input type="hidden" name="max_price" id="max_price"
                        value="{{ $maxPrice ?: 100000 }}">
                </div><!-- End .filter-price -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <button type="submit" class="btn btn-outline-primary-2 btn-block">
        <span>Apply Filters</span>
    </button>
</form>
