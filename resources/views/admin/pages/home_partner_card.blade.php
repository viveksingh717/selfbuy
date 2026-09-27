{{--
    One partner logo in Admin > Home Settings > Partners.
    $name:    input name prefix - "partners[5]" for a saved partner, "new_partners[__INDEX__]" for the add template
    $old:     old() key prefix  - "partners.5" (null for the template)
    $partner: HomePartner, or null for a new partner
--}}
@php
    $val = fn ($field, $default = '') => $old ? old("{$old}.{$field}", $partner->{$field} ?? $default) : ($partner->{$field} ?? $default);
    $status = (string) (int) $val('status', 1);
@endphp

<div class="card home-slide-card border">
    <div class="card-body py-3">
        <div class="row clearfix align-items-end">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="form-group mb-lg-0">
                    <label>Logo{{ $partner ? '' : ' *' }}</label>
                    @if ($partner && $partner->image_url)
                        <div class="setting-file-current mb-2">
                            <img src="{{ $partner->image_url }}" class="setting-file-preview" alt="{{ $partner->name }}">
                        </div>
                    @endif
                    <input type="file" class="form-control-file" name="{{ $name }}[image]" accept="image/*" {{ $partner ? '' : 'required' }}>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-sm-12">
                <div class="form-group mb-lg-0">
                    <label>Partner Name</label>
                    <input type="text" class="form-control" name="{{ $name }}[name]" value="{{ $val('name') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="form-group mb-lg-0">
                    <label>Link (e.g. /brand or full URL)</label>
                    <input type="text" class="form-control" name="{{ $name }}[link]" value="{{ $val('link', '#') }}">
                </div>
            </div>

            <div class="col-lg-1 col-md-3 col-sm-6">
                <div class="form-group mb-lg-0">
                    <label>Order</label>
                    <input type="number" min="0" class="form-control" name="{{ $name }}[sort_order]" value="{{ $val('sort_order', 0) }}">
                </div>
            </div>

            <div class="col-lg-1 col-md-3 col-sm-6">
                <div class="form-group mb-lg-0">
                    <label>Status</label>
                    <select class="form-control" name="{{ $name }}[status]">
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Show</option>
                        <option value="0" {{ $status !== '1' ? 'selected' : '' }}>Hide</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-sm-12 text-lg-right">
                @if ($partner)
                    <label class="text-danger mb-2">
                        <input type="checkbox" name="{{ $name }}[delete]" value="1"> Delete
                    </label>
                @else
                    <button type="button" class="btn btn-sm btn-outline-danger mb-2 js-remove-item">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
