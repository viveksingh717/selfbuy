@extends('admin.layouts.app')
@section('title', 'Product List')
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
                                        <a class="nav-link" href="{{ route('admin.create_product') }}">
                                            Add Product
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="{{ route('admin.product') }}">
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

                        <div class="card-body">
                            @include('partials._message')
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="productTable">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">#ID</th>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th style="width:50px;">Image</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Price</th>
                                            <th>Cost Price</th>
                                            <th>Discount</th>
                                            <th>Stock</th>
                                            <th>Flags</th>
                                            <th style="width:80px;">Status</th>
                                            <th>Created At</th>
                                            <th style="width:120px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
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
