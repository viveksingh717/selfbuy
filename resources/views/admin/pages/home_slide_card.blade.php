{{--
    One carousel slide in Admin > Home Settings > Carousel.
    $name:  input name prefix - "slides[5]" for a saved slide, "new_slides[__INDEX__]" for the add-slide template
    $old:   old() key prefix  - "slides.5" (null for the template)
    $slide: HomeSlide, or null for a new slide
--}}
@php
    $val = fn ($field, $default = '') => $old ? old("{$old}.{$field}", $slide->{$field} ?? $default) : ($slide->{$field} ?? $default);
@endphp

<div class="card home-slide-card border">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-image"></i>
            <span class="home-slide-label">{{ $slide ? 'Slide #' . $slide->id : 'New Slide' }}</span>
        </h3>
        <div class="card-options">
            @if ($slide)
                <label class="text-danger mb-0">
                    <input type="checkbox" name="{{ $name }}[delete]" value="1"> Delete this slide
                </label>
            @else
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-slide">
                    <i class="fa fa-trash"></i> Remove
                </button>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row clearfix">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Image (desktop){{ $slide ? '' : ' *' }}</label>
                    @if ($slide && $slide->image_url)
                        <div class="setting-file-current mb-2">
                            <img src="{{ $slide->image_url }}" class="setting-file-preview" alt="slide">
                        </div>
                    @endif
                    <input type="file" class="form-control-file" name="{{ $name }}[image]" accept="image/*" {{ $slide ? '' : 'required' }}>
                    <small class="text-muted d-block">{{ $slide ? 'Leave empty to keep the current image. ' : '' }}Max 4 MB.</small>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Image (mobile, optional)</label>
                    @if ($slide && $slide->image_mobile)
                        <div class="setting-file-current mb-2">
                            <img src="{{ \App\Models\HomeSlide::imageUrl($slide->image_mobile) }}" class="setting-file-preview" alt="slide mobile">
                            <label class="ml-2">
                                <input type="checkbox" name="{{ $name }}[remove_image_mobile]" value="1"> remove
                            </label>
                        </div>
                    @endif
                    <input type="file" class="form-control-file" name="{{ $name }}[image_mobile]" accept="image/*">
                    <small class="text-muted d-block">Shown on small screens; the desktop image is used if empty.</small>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Subtitle (small text above title)</label>
                    <input type="text" class="form-control" name="{{ $name }}[subtitle]" value="{{ $val('subtitle') }}">
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Title (new line = line break)</label>
                    <textarea class="form-control" name="{{ $name }}[title]" rows="2">{{ $val('title') }}</textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label>Description (optional)</label>
                    <textarea class="form-control" name="{{ $name }}[description]" rows="2">{{ $val('description') }}</textarea>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Button Name</label>
                    <input type="text" class="form-control" name="{{ $name }}[button_text]" value="{{ $val('button_text', 'SHOP NOW') }}">
                    <small class="text-muted d-block">Leave empty to hide the button.</small>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Button Link (e.g. /category-slug or full URL)</label>
                    <input type="text" class="form-control" name="{{ $name }}[button_link]" value="{{ $val('button_link', '#') }}">
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" min="0" class="form-control" name="{{ $name }}[sort_order]" value="{{ $val('sort_order', 0) }}">
                    <small class="text-muted d-block">Lower numbers show first.</small>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Status</label>
                    @php $status = (string) (int) $val('status', 1); @endphp
                    <select class="form-control show-tick" name="{{ $name }}[status]">
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Show</option>
                        <option value="0" {{ $status !== '1' ? 'selected' : '' }}>Hide</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
