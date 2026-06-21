<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ColorModel;
use App\Models\CouponModel;
use App\Models\ProductModel;
use App\Models\SizeModel;
use App\Models\TaxModel;
use App\Services\FileUploadService;
use App\Services\ProductService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->fileUploadService = new FileUploadService();
        $this->productService = new ProductService();
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('admin.products.product');
        }

        $products = ProductModel::with(['category', 'brand'])
            ->select([
                'id',
                'product_name',
                'product_slug',
                'sku',
                'product_image',
                'category_id',
                'brand_id',
                'original_price',
                'selling_price',
                'cost_price',
                'discount',
                'qty',
                'stock_status',
                'status',
                'is_featured',
                'is_trending',
                'created_at',
            ]);

        return DataTables::of($products)

            ->addColumn('product_image', function ($row) {
                $imageUrl = $row->product_image
                    ? $this->fileUploadService->getUrl('products', $row->product_image)
                    : asset('admin_assets/images/no-image.png');

                return '<img src="' . $imageUrl . '"
                            alt="' . $row->product_name . '"
                            style="width:50px; height:50px; object-fit:cover;
                                border-radius:6px; border:1px solid #dee2e6;">';
            })

            ->addColumn('product_name', function ($row) {
                return '<div>
                            <p class="mb-0 font-weight-bold" style="font-size:13px;">
                                ' . $row->product_name . '
                            </p>
                            <small class="text-muted">' . $row->product_slug . '</small>
                        </div>';
            })

            ->addColumn('category', function ($row) {
                return $row->category
                    ? $row->category->category_name
                    : '<span class="text-muted">—</span>';
            })

            ->addColumn('brand', function ($row) {
                return $row->brand
                    ? $row->brand->brand_name
                    : '<span class="text-muted">—</span>';
            })

            ->addColumn('price', function ($row) {
                $html = '<div style="line-height:1.6;">';

                if ($row->discount > 0) {
                    $html .= '<small class="text-muted" style="text-decoration:line-through;">
                                ₹' . number_format($row->original_price, 2) . '
                            </small><br>';
                    $html .= '<strong class="text-success">₹' . number_format($row->selling_price, 2) . '</strong>';
                    $html .= ' <span class="badge badge-warning" style="font-size:10px;">'
                                . $row->discount . '% off</span>';
                } else {
                    $html .= '<strong>₹' . number_format($row->selling_price, 2) . '</strong>';
                }

                $html .= '</div>';
                return $html;
            })

            ->addColumn('stock_status', function ($row) {
                $badges = [
                    'in_stock'     => '<span class="badge badge-success">In Stock</span>',
                    'out_of_stock' => '<span class="badge badge-danger">Out of Stock</span>',
                    'pre_order'    => '<span class="badge badge-info">Pre Order</span>',
                ];

                $stockBadge = $badges[$row->stock_status]
                    ?? '<span class="badge badge-secondary">N/A</span>';

                $qtyLabel = '<br><small class="text-muted">Qty: ' . $row->qty . '</small>';

                return $stockBadge . $qtyLabel;
            })

            ->addColumn('flags', function ($row) {
                $html = '';

                if ($row->is_featured) {
                    $html .= '<span class="badge badge-primary mb-1">
                                <i class="fa fa-star"></i> Featured
                            </span><br>';
                }

                if ($row->is_trending) {
                    $html .= '<span class="badge badge-warning mb-1">
                                <i class="fa fa-fire"></i> Trending
                            </span>';
                }

                return $html ?: '<span class="text-muted">—</span>';
            })

            ->addColumn('status', function ($row) {
                $checked = $row->status == 1 ? 'checked' : '';
                return '<label class="custom-switch">
                            <input type="checkbox"
                                class="custom-switch-input toggle-status"
                                data-id="' . $row->id . '" ' . $checked . '
                                data-url="/admin/product_status"
                                data-table="#productTable">
                            <span class="custom-switch-indicator"></span>
                        </label>';
            })

            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y');
            })

            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group">
                        <a href="' . route('admin.edit_product', $row->id) . '"
                            class="btn btn-sm btn-primary" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="' . route('admin.view_product', $row->id) . '"
                            class="btn btn-sm btn-info" title="View">
                            <i class="fa fa-eye"></i>
                        </a>
                        <button type="button"
                            class="btn btn-sm btn-danger delete-product"
                            data-id="' . $row->id . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })

            ->rawColumns([
                'product_image', 'product_name', 'category',
                'brand', 'price', 'stock_status', 'qty', 'flags', 'status', 'action'
            ])
            ->make(true);
    }

    public function create_product()
    {
        $categories = $this->productService->getActiveCategories();
        $brands = Brand::where('status', 1)->get();
        $colors = ColorModel::where('status', 1)->get();
        $sizes = SizeModel::where('status', 1)->get();
        $taxes = TaxModel::where('status', 1)->get();
        $coupons = CouponModel::where('status', 1)->get();

        return view('admin.products.create_product', compact('categories', 'brands', 'colors', 'sizes', 'taxes', 'coupons'));
    }

    public function product_image_upload(Request $request, ResponseService $rs)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return $rs->setValidationResponse($validator->errors()->first());
            }

            $filename = $this->fileUploadService->uploadImage(
                $request->file('product_image'),
                'products'
            );

            Log::info('Product Thumbnail Uploaded', ['file_name' => $filename]);

            return response()->json([
                'success'   => true,
                'file_name' => $filename,
                'image_url' => $this->fileUploadService->getUrl('products', $filename),
                'thumb_url' => $this->fileUploadService->getThumbUrl('products', $filename),
                'message'   => 'Thumbnail uploaded successfully',
            ]);

        } catch (Exception $e) {
            Log::error('Product Thumbnail Upload Error: ' . $e->getMessage());
            return $rs->setErrorResponse('Failed to upload thumbnail');
        }
    }

    public function product_gallery_upload(Request $request, ResponseService $rs)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gallery_image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return $rs->setValidationResponse($validator->errors()->first());
            }

            $filename = $this->fileUploadService->uploadImage(
                $request->file('gallery_image'),
                'products/gallery'
            );

            Log::info('Product Gallery Image Uploaded', ['file_name' => $filename]);

            return response()->json([
                'success'   => true,
                'file_name' => $filename,
                'image_url' => $this->fileUploadService->getUrl('products/gallery', $filename),
                'thumb_url' => $this->fileUploadService->getThumbUrl('products/gallery', $filename),
                'message'   => 'Gallery image uploaded successfully',
            ]);

        } catch (Exception $e) {
            Log::error('Product Gallery Upload Error: ' . $e->getMessage());
            return $rs->setErrorResponse('Failed to upload gallery image');
        }
    }

    public function process_product(Request $request, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }

        $validators = Validator::make($request->all(), [

            // ── Basic Info ────────────────────────────────
            'product_name'           => 'required|string|max:255',
            'product_slug'           => 'required|string|max:255|unique:product_models,product_slug',
            'sku'                    => 'nullable|string|max:100|unique:product_models,sku',
            'status'                 => 'required|in:0,1,2',
            'is_featured'            => 'required|in:0,1',
            'short_description'      => 'nullable|string',
            'description'            => 'nullable|string',
            'additional_description' => 'nullable|string',

            // ── Classification ────────────────────────────
            'category_id'            => 'required|exists:categories,id',
            'sub_category_id'        => 'nullable|exists:sub_categories,id',
            'brand_id'               => 'nullable|exists:brands,id',

            // ── Pricing ───────────────────────────────────
            'original_price'         => 'required|numeric|min:0',
            'selling_price'          => 'required|numeric|min:0',
            'cost_price'             => 'nullable|numeric|min:0',
            'discount'               => 'nullable|numeric|min:0|max:100',
            'tax_id'                 => 'nullable|exists:tax_models,id',
            'coupon_id'              => 'nullable|exists:coupon_models,id',

            // ── Inventory ─────────────────────────────────
            'qty'                    => 'required|integer|min:0',
            'low_stock_alert'        => 'nullable|integer|min:0',
            'stock_status'           => 'nullable|in:in_stock,out_of_stock,pre_order',

            // ── Images ────────────────────────────────────
            'product_image'          => 'nullable|string',
            'gallery_images'         => 'nullable|array',
            'gallery_images.*'       => 'nullable|string',

            // ── Attributes ────────────────────────────────
            'attributes'             => 'nullable|array',
            'attributes.*.color_id'  => 'nullable|exists:color_models,id',
            'attributes.*.size_id'   => 'nullable|exists:size_models,id',
            'attributes.*.stock'     => 'nullable|integer|min:0',
            'attributes.*.extra_price' => 'nullable|numeric|min:0',
            'attributes.*.sku_variant' => 'nullable|string|max:100',

            // ── SEO ───────────────────────────────────────
            'meta_title'             => 'nullable|string|max:60',
            'meta_description'       => 'nullable|string|max:160',
        ]);

        if ($validators->passes()) {
            $data = $request->only([
                'product_name', 'product_slug', 'sku',
                'status', 'is_featured',
                'short_description', 'description', 'additional_description',
                'category_id', 'sub_category_id', 'brand_id',
                'original_price', 'selling_price', 'cost_price', 'discount',
                'tax_id', 'coupon_id',
                'qty', 'low_stock_alert', 'stock_status',
                'product_image',
                'meta_title', 'meta_description',
            ]);

            // gallery & attributes separately as arrays
            $data['gallery_images'] = $request->input('gallery_images', []);
            $data['attributes']     = $request->input('attributes', []);

            $result = $this->productService->save_product($data);
            Log::info('Product Creation Attempt', ['data' => $data, 'result' => $result]);

            if ($result['status']) {
                $request->session()->flash('success', $result['message']);
                return $rs->setCreatedResponse($result['message'],$result['data']);
            } else {
                $request->session()->flash('error', $result['message']);
                return $rs->setErrorResponse($result['message']);
            }
        } else {
            return $rs->setValidationResponse($validators->errors());
        }

        return $rs->setErrorResponse($result['message'] ?? 'An unexpected error occurred, please try again.');
    }

    public function edit_product($id, ResponseService $rs)
    {
       if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Unauthorized access');
        }

        $product = $this->productService->getProductById($id);

        if (!$product) {
            return redirect()->route('admin.product')
            ->with('error', 'Product not found.');
        }

        $categories    = $this->productService->getActiveCategories();
        $subCategories = $this->productService->getSubCategoriesByCategoryId($product->category_id);
        $brands        = $this->productService->getActiveBrands();
        $colors        = $this->productService->getActiveColors();
        $sizes         = $this->productService->getActiveSizes();
        $taxes         = $this->productService->getActiveTaxes();
        $coupons       = $this->productService->getActiveCoupons();

        return view('admin.products.edit_product',
            compact('product', 'categories', 'subCategories',
                'brands', 'colors', 'sizes', 'taxes', 'coupons'));
    }

    public function update_product(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }

        $validators = Validator::make($request->all(), [

            'product_name'           => 'required|string|max:255',
            'product_slug'           => 'required|string|max:255|unique:product_models,product_slug,' . $id,
            'sku'                    => 'nullable|string|max:100|unique:product_models,sku,' . $id,
            'status'                 => 'required|in:0,1,2',
            'is_featured'            => 'nullable|in:0,1',
            'is_trending'            => 'nullable|in:0,1',
            'short_description'      => 'nullable|string',
            'description'            => 'nullable|string',

            // ── Classification ────────────────────────────
            'category_id'            => 'required|exists:categories,id',
            'sub_category_id'        => 'nullable|exists:sub_categories,id',
            'brand_id'               => 'nullable|exists:brands,id',

            // ── Pricing ───────────────────────────────────
            'original_price'         => 'required|numeric|min:0',
            'selling_price'          => 'required|numeric|min:0',
            'cost_price'             => 'nullable|numeric|min:0',
            'discount'               => 'nullable|numeric|min:0|max:100',
            'tax_id'                 => 'nullable|exists:tax_models,id',
            'coupon_id'              => 'nullable|exists:coupon_models,id',

            // ── Inventory ─────────────────────────────────
            'qty'                    => 'required|integer|min:0',
            'low_stock_alert'        => 'nullable|integer|min:0',
            'stock_status'           => 'nullable|in:in_stock,out_of_stock,pre_order',

            // ── Images ────────────────────────────────────
            'product_image'          => 'nullable|string',
            'gallery_images'         => 'nullable|array',
            'gallery_images.*'       => 'nullable|string',

            // ── Attributes ────────────────────────────────
            'attributes'             => 'nullable|array',
            'attributes.*.color_id'  => 'nullable|exists:color_models,id',
            'attributes.*.size_id'   => 'nullable|exists:size_models,id',
            'attributes.*.stock'     => 'nullable|integer|min:0',
            'attributes.*.extra_price' => 'nullable|numeric|min:0',
            'attributes.*.sku_variant' => 'nullable|string|max:100',

            // ── SEO ───────────────────────────────────────
            'meta_title'             => 'nullable|string|max:60',
            'meta_description'       => 'nullable|string|max:160',
        ]);

        if ($validators->passes()) {
            $data = $request->only([
                'product_name', 'product_slug', 'sku',
                'status', 'is_featured',
                'short_description', 'description', 'additional_description',
                'category_id', 'sub_category_id', 'brand_id',
                'original_price', 'selling_price', 'cost_price', 'discount',
                'tax_id', 'coupon_id',
                'qty', 'low_stock_alert', 'stock_status',
                'product_image',
                'meta_title', 'meta_description',
            ]);

            // gallery & attributes separately as arrays
            $data['gallery_images'] = $request->input('gallery_images', []);
            $data['attributes']     = $request->input('attributes', []);

            $result = $this->productService->update_product($id, $data);
            Log::info('Product Update Attempt', ['data' => $data, 'result' => $result]);

            if ($result['status']) {
                $request->session()->flash('success', $result['message']);
                return $rs->setSuccessResponse($result['message'],$result['data']);
            } else {
                $request->session()->flash('error', $result['message']);
                return $rs->setErrorResponse($result['message']);
            }
        } else {
            return $rs->setValidationResponse($validators->errors());
        }
    }

    public function delete_product(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $result = $this->productService->delete_product($id);

            if ($result['status']) {
                $request->session()->flash('success', $result['message']);
                return $rs->setSuccessResponse($result['message'], ['id' => $id]);
            } else {
                $request->session()->flash('error', $result['message']);
                return $rs->setErrorResponse($result['message']);
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function view_product($id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Unauthorized access');
        }

        $products = ProductModel::with([
            'category',
            'subCategory',
            'brand',
            'tax',
            'coupon',
            'galleryImages',
            'attributes.color',
            'attributes.size'
        ])->find($id);

        if (!$products) {
            return redirect()->route('admin.product')
                ->with('error', 'Product not found');
        }

        return view('admin.products.product_attribute', compact('products'));
    }

    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }

        $status = $request->input('status');
        $result = $this->productService->toggle_status($status, $id);

        if ($result['status']) {
            return $rs->setSuccessResponse($result['message'], ['id' => $id]);
        } else {
            return $rs->setErrorResponse($result['message']);
        }
    }

    public function delete_product_image($id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }

        $result = $this->productService->delete_product_image($id);

        if ($result['status']) {
            return $rs->setSuccessResponse($result['message'], ['id' => $id]);
        } else {
            return $rs->setErrorResponse($result['message']);
        }
    }

    public function delete_gallery_image($id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }

        $result = $this->productService->delete_gallery_image($id);

        if ($result['status']) {
            return $rs->setSuccessResponse($result['message'], ['id' => $id]);
        } else {
            return $rs->setErrorResponse($result['message']);
        }
    }

    public function get_subcategories($id)
    {
        $subcategories = $this->productService->getSubCategoriesByCategoryId($id);

        if ($subcategories->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No subcategories found for this category.',
                'subcategories' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }
}
