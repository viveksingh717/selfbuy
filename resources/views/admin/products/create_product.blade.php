@extends('admin.layouts.app')
@section('title', 'Add Product')
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
                                        <a class="nav-link active" href="{{ route('admin.create_product') }}">Add Product</a>
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
                            <h3 class="card-title">Add Product</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')
                            <form name="productForm" id="productForm" enctype="multipart/form-data">

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
                                                placeholder="e.g. Men's Cotton T-Shirt" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>SKU / Product Code</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="sku" id="sku"
                                                    placeholder="e.g. TSH-MEN-001" style="text-transform:uppercase;">
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
                                            <select class="form-control show-tick" name="status" id="status">
                                                <option value="">-- Status --</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                                <option value="2">Draft</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-12">
                                        <div class="form-group">
                                            <label>Is Featured</label>
                                            <select class="form-control show-tick" name="is_featured" id="is_featured">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Short Description</label>
                                            <textarea class="form-control" name="short_description" rows="2"
                                                placeholder="Brief summary shown on listing cards..."></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Full Description</label>
                                            <textarea class="form-control" name="description" rows="5"
                                                placeholder="Detailed product description, features, materials..."></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Additional Description
                                                <small class="text-muted">(optional)</small>
                                            </label>
                                            <textarea class="form-control" name="additional_description" rows="3"
                                                placeholder="Extra info, size guide, care instructions..."></textarea>
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
                                                    <option value="{{ $category->id }}">
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
                                            </select>
                                            <small class="text-muted">Select a category first</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select class="form-control show-tick" name="brand_id" id="brand_id">
                                                <option value="">-- Select Brand --</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">
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
                                                    required>
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
                                                    required>
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
                                                    id="cost_price" placeholder="0.00" min="0" step="0.01">
                                            </div>
                                            <small class="text-muted">Internal only</small>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Discount</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="discount"
                                                    id="discount" placeholder="0" min="0" max="100">
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
                                                    <option value="{{ $tax->id }}">
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
                                                    <option value="{{ $coupon->id }}">
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
                                                placeholder="e.g. 100" min="0" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Low Stock Alert</label>
                                            <input type="number" class="form-control" name="low_stock_alert"
                                                id="low_stock_alert" placeholder="e.g. 10" min="0">
                                            <small class="text-muted">Alert when stock falls below this</small>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Stock Status</label>
                                            <select class="form-control" name="stock_status" id="stock_status">
                                                <option value="in_stock">In Stock</option>
                                                <option value="out_of_stock">Out of Stock</option>
                                                <option value="pre_order">Pre Order</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>Trending</label>
                                            <select class="form-control" name="is_trending" id="is_trending">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                {{-- ══════════════════════════════════════
                                     SECTION 4 — PRODUCT ATTRIBUTES
                                                <option value="2_years">2 Years</option>
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
                                                <th style="width:28%;">Color</th>
                                                <th style="width:28%;">Size</th>
                                                <th style="width:14%;">Stock Qty</th>
                                                <th style="width:15%;">Extra Price (₹)</th>
                                                <th style="width:15%;">Variant SKU</th>
                                                <th style="width:10%; text-align:center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attributeRows">
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
                                                        name="attributes[0][sku_variant]" placeholder="e.g. TSH-RED-XL">
                                                </td>
                                                <td class="text-center">
                                                    <span class="text-muted" style="font-size:12px;">—</span>
                                                </td>
                                            </tr>
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

                                    {{-- Thumbnail → products.product_image --}}
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                Thumbnail Image
                                                <small class="text-muted">(main listing image)</small>
                                            </label>
                                            <div id="thumbnailDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop thumbnail here or click to upload
                                                </div>
                                            </div>
                                            <input type="hidden" name="product_image" id="product_image">
                                            <small class="text-muted">PNG, JPG — max 2MB. Recommended: 800×800px</small>
                                        </div>
                                    </div>

                                    {{-- Gallery → product_gallery_images table --}}
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                Gallery Images
                                                <small class="text-muted">(multiple — up to 8)</small>
                                            </label>
                                            <div id="galleryDropzone" class="dropzone border rounded p-3">
                                                <div class="dz-message">
                                                    Drag & Drop gallery images here or click to upload
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
                                                placeholder="SEO title for this product page" maxlength="60">
                                            <small class="text-muted">
                                                <span id="metaTitleCount">0</span>/60 characters
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea class="form-control" name="meta_description" id="meta_description" rows="2"
                                                placeholder="Brief description shown in search engine results..." maxlength="160"></textarea>
                                            <small class="text-muted">
                                                <span id="metaDescCount">0</span>/160 characters
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>URL Slug</label>
                                            <input type="text" class="form-control" name="product_slug"
                                                id="product_slug" placeholder="e.g. mens-cotton-t-shirt">
                                            <small class="text-muted">Auto-generated from product name</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix mt-3">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary"
                                            id="product_form_submit">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary"
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

            let rowIndex = 1;

            // ── Color options for dynamic rows ────────────────────
            const colorOptions = `<option value="">-- Color --</option>
            @foreach ($colors as $color)
                <option value="{{ $color->id }}">{{ $color->color_name }}</option>
            @endforeach`;

            // ── Size options for dynamic rows ─────────────────────
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
                        data.subcategories.forEach(sub => {
                            subSelect.innerHTML +=
                                `<option value="${sub.id}">${sub.subcategory_name}</option>`;
                        });
                    })
                    .catch(() => {
                        subSelect.innerHTML = '<option value="">-- Select Sub Category --</option>';
                    });
            });

            // ── SEO character counters ────────────────────────────
            document.getElementById('meta_title').addEventListener('input', function() {
                document.getElementById('metaTitleCount').textContent = this.value.length;
            });
            document.getElementById('meta_description').addEventListener('input', function() {
                document.getElementById('metaDescCount').textContent = this.value.length;
            });

        });

        // ── SKU auto-generate ─────────────────────────────────
        function generateSKU() {
            const productName = document.getElementById('product_name').value.trim();
            const skuInput = document.getElementById('sku');

            let prefix = 'PRD';

            if (productName) {
                // take first letters of each word — e.g. "Men's Cotton T-Shirt" → MCT
                prefix = productName
                    .split(' ')
                    .filter(word => word.length > 0)
                    .map(word => word[0].toUpperCase())
                    .join('')
                    .substring(0, 4); // max 4 chars
            }

            // random 6-char alphanumeric suffix
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let suffix = '';
            for (let i = 0; i < 6; i++) {
                suffix += chars.charAt(Math.floor(Math.random() * chars.length));
            }

            skuInput.value = prefix + '-' + suffix;
        }

        // ── Generate on button click ──────────────────────────
        document.getElementById('generateSkuBtn').addEventListener('click', function() {
            generateSKU();
        });

        // ── Auto-generate SKU when product name is typed ──────
        // (only if SKU is still empty — don't overwrite manual entry)
        document.getElementById('product_name').addEventListener('input', function() {

            // auto slug
            document.getElementById('product_slug').value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');

            // auto SKU only if field is empty
            if (!document.getElementById('sku').value.trim()) {
                generateSKU();
            }
        });

        // ── Force SKU to uppercase while typing ──────────────
        document.getElementById('sku').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection
