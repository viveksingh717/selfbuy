@extends('admin.layouts.app')
@section('title', 'Update Product')
@section('subTitle', 'Product Management')

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
                                        <a class="nav-link active" href="{{ route('admin.edit_product', $product->id) }}">
                                            Edit Product
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.product') }}">
                                            <i class="fa fa-list-ul"></i> Product List
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
                            <h3 class="card-title">Update Product</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="editProductForm" id="editProductForm" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{ $product->id }}">

                                {{-- ══════════════════════════════════════
                                     SECTION 1 — BASIC INFO
                                ══════════════════════════════════════ --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Basic Info
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-5 col-sm-12">
                                        <div class="form-group">
                                            <label>Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="product_name" id="product_name"
                                                placeholder="e.g. Men's Cotton T-Shirt" value="{{ $product->product_name }}"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>SKU / Product Code</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="sku" id="sku"
                                                    placeholder="e.g. TSH-MEN-001" style="text-transform:uppercase;"
                                                    value="{{ $product->sku }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="generateSkuBtn" title="Auto generate SKU">
                                                        <i class="fa fa-refresh"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Leave blank to auto-generate on save</small>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-12">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select class="form-control show-tick" name="status" id="status" required>
                                                <option value="">-- Status --</option>
                                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>
                                                    Inactive</option>
                                                <option value="2" {{ $product->status == 2 ? 'selected' : '' }}>Draft
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-12">
                                        <div class="form-group">
                                            <label>Is Featured</label>
                                            <select class="form-control show-tick" name="is_featured" id="is_featured">
                                                <option value="0" {{ $product->is_featured == 0 ? 'selected' : '' }}>No
                                                </option>
                                                <option value="1" {{ $product->is_featured == 1 ? 'selected' : '' }}>
                                                    Yes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" name="short_description" rows="2"
                                                placeholder="Brief summary shown on listing cards...">{{ $product->short_description }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Full Description</label>
                                            <textarea class="form-control" name="description" rows="5" placeholder="Detailed product description...">{{ $product->description }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Additional Description
                                                <small class="text-muted">(optional)</small>
                                            </label>
                                            <textarea class="form-control" name="additional_description" rows="3"
                                                placeholder="Extra info, size guide, care instructions...">{{ $product->additional_description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ══════════════════════════════════════
                                     SECTION 2 — CATEGORY & CLASSIFICATION
                                ══════════════════════════════════════ --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Category & Classification
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Category <span class="text-danger">*</span></label>
                                            <select class="form-control show-tick" name="category_id" id="category_id"
                                                required>
                                                <option value="">-- Select Category --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Sub Category</label>
                                            <select class="form-control show-tick" name="sub_category_id"
                                                id="sub_category_id">
                                                <option value="">-- Select Sub Category --</option>
                                                @foreach ($subCategories as $sub)
                                                    <option value="{{ $sub->id }}"
                                                        {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>
                                                        {{ $sub->subcategory_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select class="form-control show-tick" name="brand_id" id="brand_id">
                                                <option value="">-- Select Brand --</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->brand_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ══════════════════════════════════════
                                     SECTION 3 — PRICING & STOCK
                                ══════════════════════════════════════ --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Pricing & Stock
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Regular Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="number" class="form-control" name="original_price"
                                                    id="original_price" placeholder="0.00" min="0" step="0.01"
                                                    required value="{{ $product->original_price }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Selling Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="number" class="form-control" name="selling_price"
                                                    id="selling_price" placeholder="0.00" min="0" step="0.01"
                                                    required value="{{ $product->selling_price }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Cost Price</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="number" class="form-control" name="cost_price"
                                                    id="cost_price" placeholder="0.00" min="0" step="0.01"
                                                    value="{{ $product->cost_price }}">
                                            </div>
                                            <small class="text-muted">Internal only</small>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Discount</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="discount"
                                                    id="discount" placeholder="0" min="0" max="100"
                                                    value="{{ $product->discount }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Tax</label>
                                            <select class="form-control" name="tax_id" id="tax_id">
                                                <option value="">-- No Tax --</option>
                                                @foreach ($taxes as $tax)
                                                    <option value="{{ $tax->id }}"
                                                        {{ $product->tax_id == $tax->id ? 'selected' : '' }}>
                                                        {{ $tax->tax_name }}
                                                        ({{ $tax->tax_rate }}{{ $tax->tax_type === 'fixed' ? '₹' : '%' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Coupon</label>
                                            <select class="form-control" name="coupon_id" id="coupon_id">
                                                <option value="">-- No Coupon --</option>
                                                @foreach ($coupons as $coupon)
                                                    <option value="{{ $coupon->id }}"
                                                        {{ $product->coupon_id == $coupon->id ? 'selected' : '' }}>
                                                        {{ $coupon->coupon_name }} ({{ $coupon->coupon_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Stock Quantity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="qty" id="qty"
                                                placeholder="e.g. 100" min="0" required
                                                value="{{ $product->qty }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Low Stock Alert</label>
                                            <input type="number" class="form-control" name="low_stock_alert"
                                                id="low_stock_alert" placeholder="e.g. 10" min="0"
                                                value="{{ $product->low_stock_alert }}">
                                            <small class="text-muted">Alert when stock falls below this</small>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Stock Status</label>
                                            <select class="form-control" name="stock_status" id="stock_status">
                                                <option value="in_stock"
                                                    {{ $product->stock_status === 'in_stock' ? 'selected' : '' }}>In
                                                    Stock</option>
                                                <option value="out_of_stock"
                                                    {{ $product->stock_status === 'out_of_stock' ? 'selected' : '' }}>Out
                                                    of Stock</option>
                                                <option value="pre_order"
                                                    {{ $product->stock_status === 'pre_order' ? 'selected' : '' }}>Pre
                                                    Order</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- is_trending if you have it --}}
                                    <div class="col-md-2 col-sm-12">
                                        <div class="form-group">
                                            <label>Is Trending</label>
                                            <select class="form-control show-tick" name="is_trending" id="is_trending">
                                                <option value="0" {{ $product->is_trending == 0 ? 'selected' : '' }}>
                                                    No</option>
                                                <option value="1" {{ $product->is_trending == 1 ? 'selected' : '' }}>
                                                    Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ══════════════════════════════════════
                                     SECTION 4 — PRODUCT ATTRIBUTES
                                ══════════════════════════════════════ --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Product Attributes
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="attributeTable">
                                        <thead style="background:#f8f9fa;">
                                            <tr>
                                                <th style="width:25%;">Color</th>
                                                <th style="width:25%;">Size</th>
                                                <th style="width:13%;">Stock Qty</th>
                                                <th style="width:14%;">Extra Price (₹)</th>
                                                <th style="width:15%;">Variant SKU</th>
                                                <th style="width:8%; text-align:center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attributeRows">

                                            {{-- pre-fill saved attributes --}}
                                            @forelse ($product->attributes as $index => $attr)
                                                <tr class="attribute-row">
                                                    <td>
                                                        <select class="form-control"
                                                            name="attributes[{{ $index }}][color_id]">
                                                            <option value="">-- Color --</option>
                                                            @foreach ($colors as $color)
                                                                <option value="{{ $color->id }}"
                                                                    {{ $attr->color_id == $color->id ? 'selected' : '' }}>
                                                                    {{ $color->color_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control"
                                                            name="attributes[{{ $index }}][size_id]">
                                                            <option value="">-- Size --</option>
                                                            @foreach ($sizes as $size)
                                                                <option value="{{ $size->id }}"
                                                                    {{ $attr->size_id == $size->id ? 'selected' : '' }}>
                                                                    {{ $size->size_name }} ({{ $size->size_code }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="attributes[{{ $index }}][stock]" placeholder="0"
                                                            min="0" value="{{ $attr->stock }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="attributes[{{ $index }}][extra_price]"
                                                            placeholder="0.00" min="0" step="0.01"
                                                            value="{{ $attr->extra_price }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="attributes[{{ $index }}][sku_variant]"
                                                            placeholder="e.g. TSH-RED-XL"
                                                            value="{{ $attr->sku_variant }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-attribute-row"
                                                            title="Remove row">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                {{-- empty first row if no attributes saved --}}
                                                <tr class="attribute-row">
                                                    <td>
                                                        <select class="form-control" name="attributes[0][color_id]">
                                                            <option value="">-- Color --</option>
                                                            @foreach ($colors as $color)
                                                                <option value="{{ $color->id }}">
                                                                    {{ $color->color_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="attributes[0][size_id]">
                                                            <option value="">-- Size --</option>
                                                            @foreach ($sizes as $size)
                                                                <option value="{{ $size->id }}">
                                                                    {{ $size->size_name }} ({{ $size->size_code }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="attributes[0][stock]" placeholder="0" min="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="attributes[0][extra_price]" placeholder="0.00"
                                                            min="0" step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="attributes[0][sku_variant]"
                                                            placeholder="e.g. TSH-RED-XL">
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-muted" style="font-size:12px;">—</span>
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="addAttributeRow">
                                    <i class="fa fa-plus"></i> Add More
                                </button>

                                <hr class="mt-3 mb-3">

                                {{-- ══════════════════════════════════════
                                     SECTION 5 — PRODUCT IMAGES
                                ══════════════════════════════════════ --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    Product Images
                                </h6>
                                <div class="row clearfix">

                                    {{-- Thumbnail --}}
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                Thumbnail Image
                                                <small class="text-muted">(main listing image)</small>
                                            </label>

                                            {{-- show existing thumbnail --}}
                                            @if ($product->product_image)
                                                <div class="mb-2" id="existingThumbnail">
                                                    <img src="{{ asset('storage/products/thumb/' . $product->product_image) }}"
                                                        alt="Thumbnail"
                                                        style="width:80px; height:80px; object-fit:cover;
                                                               border-radius:6px; border:1px solid #dee2e6;">
                                                    <br>
                                                    <small class="text-muted">Current thumbnail.
                                                        Upload new to replace.</small>
                                                </div>
                                            @endif

                                            <div id="thumbnailDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop thumbnail here or click to replace
                                                </div>
                                            </div>
                                            <input type="hidden" name="product_image" id="product_image"
                                                value="{{ $product->product_image }}">
                                            <small class="text-muted">PNG, JPG — max 2MB</small>
                                        </div>
                                    </div>

                                    {{-- Gallery --}}
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                Gallery Images
                                                <small class="text-muted">(multiple — up to 8)</small>
                                            </label>

                                            {{-- show existing gallery --}}
                                            @if ($product->galleryImages->count())
                                                <div class="mb-2 d-flex flex-wrap" style="gap:6px;" id="existingGallery">
                                                    @foreach ($product->galleryImages as $img)
                                                        <div class="position-relative"
                                                            id="gallery-item-{{ $img->id }}">
                                                            <img src="{{ asset('storage/products/gallery/' . $img->image_path) }}"
                                                                alt="Gallery"
                                                                style="width:70px; height:70px; object-fit:cover;
                                                                       border-radius:6px; border:1px solid #dee2e6;">
                                                            <button type="button"
                                                                class="btn btn-xs btn-danger remove-gallery-image"
                                                                data-id="{{ $img->id }}"
                                                                style="position:absolute; top:-6px; right:-6px;
                                                                       width:18px; height:18px; padding:0;
                                                                       font-size:10px; border-radius:50%;">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <small class="text-muted d-block mb-2">
                                                    Click × to remove existing. Upload new below.
                                                </small>
                                            @endif

                                            <div id="galleryDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop new gallery images here
                                                </div>
                                            </div>
                                            <div id="galleryImageNames"></div>
                                            <small class="text-muted">PNG, JPG — max 2MB each</small>
                                        </div>
                                    </div>

                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ══════════════════════════════════════
                                     SECTION 6 — SEO
                                ══════════════════════════════════════ --}}
                                <h6 class="text-muted font-weight-bold mb-3"
                                    style="font-size:11px; text-transform:uppercase; letter-spacing:.06em;">
                                    SEO
                                </h6>
                                <div class="row clearfix">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="meta_title"
                                                placeholder="SEO title for this product page" maxlength="60"
                                                value="{{ $product->meta_title }}">
                                            <small class="text-muted">
                                                <span
                                                    id="metaTitleCount">{{ strlen($product->meta_title ?? '') }}</span>/60
                                                characters
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea class="form-control" name="meta_description" id="meta_description" rows="2"
                                                placeholder="Brief description shown in search results..." maxlength="160">{{ $product->meta_description }}</textarea>
                                            <small class="text-muted">
                                                <span
                                                    id="metaDescCount">{{ strlen($product->meta_description ?? '') }}</span>/160
                                                characters
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>URL Slug</label>
                                            <input type="text" class="form-control" name="product_slug"
                                                id="product_slug" placeholder="e.g. mens-cotton-t-shirt"
                                                value="{{ $product->product_slug }}">
                                            <small class="text-muted">Auto-generated from product name</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary"
                                            id="product_editform_submit">Update</button>
                                        <button type="button" class="btn btn-outline-secondary"
                                            id="product_form_cancel">Cancel</button>
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

            let rowIndex = {{ $product->attributes->count() ?: 1 }};

            const colorOptions = `<option value="">-- Color --</option>
        @foreach ($colors as $color)
            <option value="{{ $color->id }}">{{ $color->color_name }}</option>
        @endforeach`;

            const sizeOptions = `<option value="">-- Size --</option>
        @foreach ($sizes as $size)
            <option value="{{ $size->id }}">{{ $size->size_name }} ({{ $size->size_code }})</option>
        @endforeach`;

            // ── Add attribute row ─────────────────────────────────
            document.getElementById('addAttributeRow').addEventListener('click', function() {
                const tbody = document.getElementById('attributeRows');
                const tr = document.createElement('tr');
                tr.className = 'attribute-row';
                tr.dataset.index = rowIndex;
                tr.innerHTML = `
            <td>
                <select class="form-control" name="attributes[${rowIndex}][color_id]">
                    ${colorOptions}
                </select>
            </td>
            <td>
                <select class="form-control" name="attributes[${rowIndex}][size_id]">
                    ${sizeOptions}
                </select>
            </td>
            <td>
                <input type="number" class="form-control"
                    name="attributes[${rowIndex}][stock]"
                    placeholder="0" min="0">
            </td>
            <td>
                <input type="number" class="form-control"
                    name="attributes[${rowIndex}][extra_price]"
                    placeholder="0.00" min="0" step="0.01">
            </td>
            <td>
                <input type="text" class="form-control"
                    name="attributes[${rowIndex}][sku_variant]"
                    placeholder="e.g. TSH-RED-XL">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-attribute-row"
                    title="Remove row">
                    <i class="fa fa-trash"></i>
                </button>
            </td>`;
                tbody.appendChild(tr);
                rowIndex++;
            });

            // ── Remove attribute row ──────────────────────────────
            document.getElementById('attributeRows').addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-attribute-row');
                if (!btn) return;
                btn.closest('tr').remove();
            });

            // ── Category → Sub Category AJAX ─────────────────────
            document.getElementById('category_id').addEventListener('change', function() {
                const id = this.value;
                const subSelect = document.getElementById('sub_category_id');
                subSelect.innerHTML = '<option value="">-- Loading... --</option>';
                if (!id) {
                    subSelect.innerHTML = '<option value="">-- Select Sub Category --</option>';
                    return;
                }
                fetch('/admin/get_subcategories/' + id)
                    .then(res => res.json())
                    .then(data => {
                        subSelect.innerHTML = '<option value="">-- Select Sub Category --</option>';
                        data.forEach(sub => {
                            const selected = sub.id ==
                                {{ $product->sub_category_id ?? 'null' }} ?
                                'selected' : '';
                            subSelect.innerHTML +=
                                `<option value="${sub.id}" ${selected}>${sub.sub_category_name}</option>`;
                        });
                    })
                    .catch(() => {
                        subSelect.innerHTML = '<option value="">-- Select Sub Category --</option>';
                    });
            });

            // ── Slug sync when name changes ───────────────────────
            document.getElementById('product_name').addEventListener('input', function() {
                document.getElementById('product_slug').value = this.value
                    .toLowerCase().trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            });

            // ── SKU generate ──────────────────────────────────────
            function generateSKU() {
                const name = document.getElementById('product_name').value.trim();
                let prefix = 'PRD';
                if (name) {
                    prefix = name.split(' ')
                        .filter(w => w.length > 0)
                        .map(w => w[0].toUpperCase())
                        .join('')
                        .substring(0, 4);
                }
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let suffix = '';
                for (let i = 0; i < 6; i++) {
                    suffix += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                document.getElementById('sku').value = prefix + '-' + suffix;
            }

            document.getElementById('generateSkuBtn').addEventListener('click', generateSKU);

            document.getElementById('sku').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // ── SEO counters ──────────────────────────────────────
            document.getElementById('meta_title').addEventListener('input', function() {
                document.getElementById('metaTitleCount').textContent = this.value.length;
            });
            document.getElementById('meta_description').addEventListener('input', function() {
                document.getElementById('metaDescCount').textContent = this.value.length;
            });

            // ── Remove existing gallery image via AJAX ────────────
            $(document).on('click', '.remove-gallery-image', function() {
                const id = $(this).data('id');
                const item = $('#gallery-item-' + id);

                $.ajax({
                    url: '/admin/delete_gallery_image/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status) {
                            item.remove();
                            toastr.success(res.message);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function() {
                        toastr.error('Failed to remove image.');
                    }
                });
            });

            // ── Cancel button ─────────────────────────────────────
            document.getElementById('product_form_cancel').addEventListener('click', function() {
                window.location.href = '{{ route('admin.product') }}';
            });

            // ── AJAX submit ───────────────────────────────────────
            $('#editProductForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('input[name="id"]').val();
                const btn = $('#product_editform_submit');

                btn.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: '/admin/update_product/' + id,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            setTimeout(() => {
                                window.location.href = '{{ route('admin.product') }}';
                            }, 1200);
                        } else {
                            toastr.error(res.message);
                            btn.prop('disabled', false).text('Update');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(err => toastr.error(err[0]));
                        } else {
                            toastr.error('Something went wrong.');
                        }
                        btn.prop('disabled', false).text('Update');
                    }
                });
            });

        });
    </script>
@endsection
