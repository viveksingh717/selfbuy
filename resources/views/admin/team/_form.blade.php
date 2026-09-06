@php $m = $member ?? null; @endphp

<form name="{{ $formId }}" id="{{ $formId }}" class="card-body" enctype="multipart/form-data">
    @if ($m)
        <input type="hidden" name="id" value="{{ $m->id }}">
    @endif

    <div class="row clearfix">
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name"
                    value="{{ old('name', $m->name ?? '') }}" placeholder="e.g. Vivek Singh">
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Designation</label>
                <input type="text" class="form-control" name="designation" id="designation"
                    value="{{ old('designation', $m->designation ?? '') }}" placeholder="e.g. Founder &amp; Owner">
            </div>
        </div>

        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" min="0" class="form-control" name="sort_order" id="sort_order"
                    value="{{ old('sort_order', $m->sort_order ?? 0) }}">
                <small class="text-muted">Lower shows first</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Status <span class="text-danger">*</span></label>
                <select class="form-control show-tick" name="status" id="status">
                    <option value="1" {{ (string) old('status', $m->status ?? 1) === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (string) old('status', $m->status ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" id="email"
                    value="{{ old('email', $m->email ?? '') }}">
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" id="phone"
                    value="{{ old('phone', $m->phone ?? '') }}">
            </div>
        </div>

        <div class="col-md-8 col-sm-12">
            <div class="form-group">
                <label>Short Bio</label>
                <textarea class="form-control" name="bio" id="bio" rows="3"
                    placeholder="One or two lines shown on the card overlay">{{ old('bio', $m->bio ?? '') }}</textarea>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Photo</label>
                <input type="file" class="form-control-file" name="photo" id="photo" accept="image/*">
                <small class="text-muted d-block">JPG / PNG / WEBP, max 2 MB. Square works best.</small>
                <img id="photoPreview"
                    src="{{ $m && $m->photo ? $m->photo_url : '' }}"
                    style="{{ $m && $m->photo ? '' : 'display:none;' }}width:70px;height:70px;border-radius:50%;object-fit:cover;margin-top:8px;">
            </div>
        </div>

        <div class="col-12"><hr class="mt-1 mb-3"></div>
        <div class="col-12">
            <h6 class="text-muted font-weight-bold mb-3"
                style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Social Links</h6>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Facebook URL</label>
                <input type="url" class="form-control" name="facebook_url" id="facebook_url"
                    value="{{ old('facebook_url', $m->facebook_url ?? '') }}" placeholder="https://facebook.com/…">
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Twitter / X URL</label>
                <input type="url" class="form-control" name="twitter_url" id="twitter_url"
                    value="{{ old('twitter_url', $m->twitter_url ?? '') }}" placeholder="https://x.com/…">
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>Instagram URL</label>
                <input type="url" class="form-control" name="instagram_url" id="instagram_url"
                    value="{{ old('instagram_url', $m->instagram_url ?? '') }}" placeholder="https://instagram.com/…">
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label>LinkedIn URL</label>
                <input type="url" class="form-control" name="linkedin_url" id="linkedin_url"
                    value="{{ old('linkedin_url', $m->linkedin_url ?? '') }}" placeholder="https://linkedin.com/in/…">
            </div>
        </div>
    </div>

    <div class="row clearfix mt-2">
        <div class="col-sm-12">
            <button type="submit" class="btn btn-primary" id="team_form_submit">{{ $submitLabel }}</button>
            <a href="{{ route('admin.team_members') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>

<script>
    (function () {
        var input = document.getElementById('photo');
        var preview = document.getElementById('photoPreview');
        if (!input) return;
        input.addEventListener('change', function () {
            var f = this.files && this.files[0];
            if (!f) return;
            preview.src = URL.createObjectURL(f);
            preview.style.display = 'inline-block';
        });
    })();
</script>
