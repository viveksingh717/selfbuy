@php $g = $item ?? null; @endphp

<form name="{{ $formId }}" id="{{ $formId }}" class="card-body" enctype="multipart/form-data">
    @if ($g)
        <input type="hidden" name="id" value="{{ $g->id }}">
    @endif

    <div class="row clearfix">
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="form-control" name="title" id="title"
                    value="{{ old('title', $g->title ?? '') }}" placeholder="Optional caption / alt text">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Album</label>
                <input type="text" class="form-control" name="album" id="album"
                    value="{{ old('album', $g->album ?? '') }}" placeholder="e.g. Store, Events">
                <small class="text-muted">Free-text grouping</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" min="0" class="form-control" name="sort_order" id="sort_order"
                    value="{{ old('sort_order', $g->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Link URL</label>
                <input type="url" class="form-control" name="link_url" id="link_url"
                    value="{{ old('link_url', $g->link_url ?? '') }}" placeholder="https://… (optional)">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Status <span class="text-danger">*</span></label>
                <select class="form-control show-tick" name="status" id="status">
                    <option value="1" {{ (string) old('status', $g->status ?? 1) === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (string) old('status', $g->status ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label>Caption</label>
                <textarea class="form-control" name="caption" id="caption" rows="2"
                    placeholder="Longer description (optional)">{{ old('caption', $g->caption ?? '') }}</textarea>
            </div>
        </div>

        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label>Image @if (!$g)<span class="text-danger">*</span>@endif</label>
                <input type="file" class="form-control-file" name="image" id="image" accept="image/*">
                <small class="text-muted d-block">JPG / PNG / WEBP / GIF, max 4 MB.
                    @if ($g) Leave empty to keep the current image. @endif
                </small>
                <img id="imagePreview"
                    src="{{ $g ? $g->image_url : '' }}"
                    style="{{ $g ? '' : 'display:none;' }}max-width:200px;max-height:140px;border-radius:6px;object-fit:cover;margin-top:10px;">
            </div>
        </div>
    </div>

    <div class="row clearfix mt-2">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-primary" id="gallery_form_submit">{{ $submitLabel }}</button>
            <a href="{{ route('admin.gallery') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>

<script>
    (function () {
        var input = document.getElementById('image');
        var preview = document.getElementById('imagePreview');
        if (!input) return;
        input.addEventListener('change', function () {
            var f = this.files && this.files[0];
            if (!f) return;
            preview.src = URL.createObjectURL(f);
            preview.style.display = 'inline-block';
        });
    })();
</script>
