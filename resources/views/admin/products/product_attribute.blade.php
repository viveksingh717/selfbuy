@extends('admin.layouts.app')
@section('title', 'View Product')
@section('subTitle', 'Product Details')

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
                                        <a class="nav-link" href="{{ route('admin.create_product') }}">
                                            Add Product
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.product') }}">
                                            <i class="fa fa-list-ul"></i> Product List
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="{{ route('admin.view_product', $products->id) }}">
                                            <i class="fa fa-eye"></i> View Product
                                        </a>
                                    </li>
                                </ul>

                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('admin.edit_product', $products->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.product') }}" class="btn btn-outline-secondary btn-sm ml-2">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>

                            <div class="row">

                                {{-- Main Image --}}
                                <div class="col-md-4">
                                    @if ($products->product_image)
                                        <img src="{{ asset('storage/products/thumb/' . $products->product_image) }}"
                                            class="img-fluid border rounded">
                                    @else
                                        <img src="{{ asset('photo.png') }}" class="img-fluid border rounded">
                                    @endif
                                </div>

                                {{-- Product Details --}}
                                <div class="col-md-8">

                                    <table class="table table-bordered">

                                        <tr>
                                            <th width="250">Product Name</th>
                                            <td>{{ $products->product_name }}</td>
                                        </tr>

                                        <tr>
                                            <th>Slug</th>
                                            <td>{{ $products->product_slug }}</td>
                                        </tr>

                                        <tr>
                                            <th>Category</th>
                                            <td>{{ $products->category->category_name ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Sub Category</th>
                                            <td>{{ $products->subCategory->subcategory_name ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Brand</th>
                                            <td>{{ $products->brand->brand_name ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Tax</th>
                                            <td>{{ $products->tax->tax_name ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Coupon</th>
                                            <td>{{ $products->coupon->coupon_name ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>SKU</th>
                                            <td>{{ $products->sku ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Price</th>
                                            <td>₹{{ number_format($products->price, 2) }}</td>
                                        </tr>

                                        <tr>
                                            <th>Sale Price</th>
                                            <td>₹{{ number_format($products->sale_price, 2) }}</td>
                                        </tr>

                                        <tr>
                                            <th>Stock Quantity</th>
                                            <td>{{ $products->qty }}</td>
                                        </tr>

                                        <tr>
                                            <th>Stock Status</th>
                                            <td>
                                                <span class="badge badge-success">
                                                    {{ ucwords(str_replace('_', ' ', $products->stock_status)) }}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if ($products->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>

                                    </table>

                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Description</h5>
                                    <div class="border rounded p-3">
                                        {!! $products->description !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Gallery Images --}}
                            @if ($products->galleryImages->count())
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h5>Gallery Images</h5>

                                        <div class="d-flex flex-wrap gap-2">

                                            @foreach ($products->galleryImages as $image)
                                                <img src="{{ asset('storage/products/gallery/' . $image->image_path) }}"
                                                    width="120" height="120"
                                                    style="object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Variants --}}
                            @if ($products->attributes->count())
                                <div class="row mt-4">
                                    <div class="col-md-12">

                                        <h5>Product Variants</h5>

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Color</th>
                                                    <th>Size</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($products->attributes as $attribute)
                                                    <tr>
                                                        <td>{{ $attribute->color->color_name ?? '-' }}</td>
                                                        <td>{{ $attribute->size->size_name ?? '-' }}</td>
                                                        <td>₹{{ $attribute->extra_price ?? 0 }}</td>
                                                        <td>{{ $attribute->stock ?? 0 }}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            @endif

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
                            <h3 class="card-title">View Product</h3>
                        </div>
                        <div class="card-body">
                            @include('partials._message')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script></script>
@endsection
