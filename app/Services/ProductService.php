<?php
namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ColorModel;
use App\Models\CouponModel;
use App\Models\ProductAttribute;
use App\Models\ProductGalleryImage;
use App\Models\ProductModel;
use App\Models\SizeModel;
use App\Models\SubCategory;
use App\Models\TaxModel;
use App\Services\FileUploadService;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductService {
    private $fileUploadService;

    public function __construct() {
        $this->fileUploadService = new FileUploadService();
    }

    public function save_product($data)
    {
        try {

            // ── Auto-generate SKU if not provided ─────────
            if (empty($data['sku'])) {
                $data['sku'] = 'PRD-' . strtoupper(uniqid());
            }

            // ── Save main product ─────────────────────────
            $product = new ProductModel();

            $product->product_name           = $data['product_name'];
            $product->product_slug           = $data['product_slug'];
            $product->sku                    = $data['sku'];
            $product->short_description      = $data['short_description']      ?? null;
            $product->description            = $data['description']            ?? null;
            $product->additional_description = $data['additional_description'] ?? null;

            // classification
            $product->category_id            = $data['category_id'];
            $product->sub_category_id        = $data['sub_category_id']        ?? null;
            $product->brand_id               = $data['brand_id']               ?? null;

            // pricing
            $product->original_price         = $data['original_price'];
            $product->selling_price          = $data['selling_price'];
            $product->cost_price             = $data['cost_price']             ?? 0;
            $product->discount               = $data['discount']               ?? 0;
            $product->tax_id                 = $data['tax_id']                 ?? null;
            $product->coupon_id              = $data['coupon_id']              ?? null;

            // inventory
            $product->qty                    = $data['qty'];
            $product->low_stock_alert        = $data['low_stock_alert']        ?? 0;
            $product->stock_status           = $data['stock_status']           ?? 'in_stock';

            // image (thumbnail stored in products table)
            $product->product_image          = $data['product_image']          ?? null;

            // flags
            $product->status                 = $data['status'];
            $product->is_featured            = $data['is_featured'];

            // SEO
            $product->meta_title             = $data['meta_title']             ?? null;
            $product->meta_description       = $data['meta_description']       ?? null;

            $product->save();

            // ── Save gallery images ───────────────────────
            if (!empty($data['gallery_images'])) {
                foreach ($data['gallery_images'] as $index => $imageName) {
                    if (empty($imageName)) continue;

                    ProductGalleryImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imageName,
                        'sort_order' => $index,
                    ]);
                }
            }

            // ── Save product attributes ───────────────────
            if (!empty($data['attributes'])) {
                foreach ($data['attributes'] as $attr) {

                    // skip completely empty rows
                    if (empty($attr['color_id']) && empty($attr['size_id'])) {
                        continue;
                    }

                    ProductAttribute::create([
                        'product_id'  => $product->id,
                        'color_id'    => $attr['color_id']    ?? null,
                        'size_id'     => $attr['size_id']     ?? null,
                        'stock'       => $attr['stock']       ?? 0,
                        'extra_price' => $attr['extra_price'] ?? 0,
                        'sku_variant' => $attr['sku_variant'] ?? null,
                        'status'      => 1,
                    ]);
                }
            }

            return [
                'status'  => true,
                'message' => 'Product saved successfully.',
                'data'    => $product->load(['galleryImages', 'attributes']),
            ];

        } catch (Exception $e) {

            Log::error('Product Save Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Failed to save product.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function update_product($id, $data)
    {
        try {
            $product = ProductModel::find($id);

            if (!$product) {
                return [
                    'status'  => false,
                    'message' => 'Product not found.',
                    'data'    => [],
                ];
            }

            // ── Auto-generate SKU if cleared ──────────────
            if (empty($data['sku'])) {
                $words  = explode(' ', trim($data['product_name']));
                $prefix = strtoupper(implode('', array_map(fn($w) => $w[0], $words)));
                $prefix = substr($prefix, 0, 4);

                do {
                    $suffix = strtoupper(substr(md5(uniqid()), 0, 6));
                    $sku    = $prefix . '-' . $suffix;
                } while (ProductModel::where('sku', $sku)->where('id', '!=', $id)->exists());

                $data['sku'] = $sku;
            }

            // ── Update main product fields ────────────────
            $product->product_name           = $data['product_name'];
            $product->product_slug           = $data['product_slug'];
            $product->sku                    = $data['sku'];
            $product->short_description      = $data['short_description']      ?? null;
            $product->description            = $data['description']            ?? null;

            // classification
            $product->category_id            = $data['category_id'];
            $product->sub_category_id        = $data['sub_category_id']        ?? null;
            $product->brand_id               = $data['brand_id']               ?? null;

            // pricing
            $product->original_price         = $data['original_price'];
            $product->selling_price          = $data['selling_price'];
            $product->cost_price             = $data['cost_price']             ?? 0;
            $product->discount               = $data['discount']               ?? 0;
            $product->tax_id                 = $data['tax_id']                 ?? null;
            $product->coupon_id              = $data['coupon_id']              ?? null;

            // inventory
            $product->qty                    = $data['qty'];
            $product->low_stock_alert        = $data['low_stock_alert']        ?? 0;
            $product->stock_status           = $data['stock_status']           ?? 'in_stock';

            // image — only update if new one uploaded
            if (!empty($data['product_image']) &&
                $data['product_image'] !== $product->product_image) {

                // delete old thumbnail using shared service
                if ($product->product_image) {

                    $this->fileUploadService->deleteImage(
                        basename($product->product_image),
                        'products/thumb'
                    );
                }

                $product->product_image = $data['product_image'];
            }

            // flags
            $product->status                 = $data['status'];
            $product->is_featured            = $data['is_featured']            ?? 0;
            $product->is_trending            = $data['is_trending']            ?? 0;

            // SEO
            $product->meta_title             = $data['meta_title']             ?? null;
            $product->meta_description       = $data['meta_description']       ?? null;

            $product->save();

            // ── Append new gallery images ─────────────
            if (!empty($data['gallery_images'])) {
                $existingCount = ProductGalleryImage::where('product_id', $id)->count();

                foreach ($data['gallery_images'] as $index => $imageName) {
                    if (empty($imageName)) continue;

                    ProductGalleryImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imageName,
                        'sort_order' => $existingCount + $index,
                    ]);
                }
            }

            // ── Update attributes ─────────────────────────
            if (isset($data['attributes'])) {
                ProductAttribute::where('product_id', $id)->delete();

                foreach ($data['attributes'] as $attr) {
                    if (empty($attr['color_id']) && empty($attr['size_id'])) continue;

                    ProductAttribute::create([
                        'product_id'  => $product->id,
                        'color_id'    => $attr['color_id']    ?? null,
                        'size_id'     => $attr['size_id']     ?? null,
                        'stock'       => $attr['stock']       ?? 0,
                        'extra_price' => $attr['extra_price'] ?? 0,
                        'sku_variant' => $attr['sku_variant'] ?? null,
                        'status'      => 1,
                    ]);
                }
            }

            return [
                'status'  => true,
                'message' => 'Product updated successfully.',
                'data'    => $product->load(['galleryImages', 'attributes']),
            ];

        } catch (Exception $e) {
            Log::error('Product Update Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to update product.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    // ── Delete gallery image ──────────────────────────────
    public function delete_gallery_image($id): array
    {
        try {
            $image = ProductGalleryImage::find($id);

            if (!$image) {
                return ['status' => false, 'message' => 'Image not found.'];
            }

            // delete from storage using shared service
            $this->fileUploadService->deleteImage(
                basename($image->image_path),
                'products/gallery'
            );

            $image->delete();

            return ['status' => true, 'message' => 'Image removed successfully.'];

        } catch (Exception $e) {
            Log::error('Gallery Image Delete Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to remove image.',
                'error'   => $e->getMessage(),
            ];
        }
    }

     // ── Delete product (with all images) ─────────────────
    public function delete_product($id): array
    {
        try {
            $product = ProductModel::find($id);

            if (!$product) {
                return ['status' => false, 'message' => 'Product not found.'];
            }

            // delete thumbnail
            if ($product->product_image) {
                $this->fileUploadService->deleteImage(
                    basename($product->product_image),
                    'products/thumb'
                );
            }

            // delete all gallery images
            $galleryImages = ProductGalleryImage::where('product_id', $id)->get();
            foreach ($galleryImages as $img) {
                $this->fileUploadService->deleteImage(
                    basename($img->image_path),
                    'products/gallery'
                );
            }

            // delete DB records
            ProductGalleryImage::where('product_id', $id)->delete();
            ProductAttribute::where('product_id', $id)->delete();

            // soft delete product
            $product->delete();

            return ['status' => true, 'message' => 'Product deleted successfully.'];

        } catch (Exception $e) {
            Log::error('Product Delete Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to delete product.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    // SubCategory related services can be added here
    public function getActiveCategories()
    {
        return Category::query()
            ->where('status', 1)
            ->orderBy('category_name')
            ->get();
    }

    public function getActiveSubCategories()
    {
        return SubCategory::query()
            ->where('status', 1)
            ->orderBy('subcategory_name')
            ->get();
    }

    public function getSubCategoriesByCategoryId($categoryId)
    {
        return SubCategory::query()
            ->where('category_id', $categoryId)
            ->where('status', 1)
            ->orderBy('subcategory_name')
            ->get();
    }

    public function getProductById($id)
    {
        return ProductModel::with(['galleryImages', 'attributes'])->find($id);
    }

    public function getProductsByCategory($categoryId, $subCategoryId = null, array $filters = [], $perPage = 2)
    {
        $query = ProductModel::query()
            ->with(['category', 'subCategory'])
            ->where('category_id', $categoryId)
            ->where('status', 1);

        if (!empty($subCategoryId)) {
            $query->where('sub_category_id', $subCategoryId);
        }

        if (!empty($filters['brand_ids'])) {
            $query->whereIn('brand_id', $filters['brand_ids']);
        }

        if (!empty($filters['color_ids'])) {
            $query->whereHas('attributes', function ($q) use ($filters) {
                $q->whereIn('color_id', $filters['color_ids']);
            });
        }

        if (!empty($filters['size_ids'])) {
            $query->whereHas('attributes', function ($q) use ($filters) {
                $q->whereIn('size_id', $filters['size_ids']);
            });
        }

        if (is_numeric($filters['min_price'] ?? null)) {
            $query->where('selling_price', '>=', $filters['min_price']);
        }

        if (is_numeric($filters['max_price'] ?? null)) {
            $query->where('selling_price', '<=', $filters['max_price']);
        }

        switch ($filters['sort'] ?? 'latest') {
            case 'price_low':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'name':
                $query->orderBy('product_name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getActiveBrands()
    {
        return Brand::query()
            ->where('status', 1)
            ->orderBy('brand_name')
            ->get();
    }

    public function getProductAttributes($productId)
    {
        return ProductAttribute::query()
            ->where('product_id', $productId)
            ->get();
    }

    public function getActiveColors()
    {
        return ColorModel::query()
            ->where('status', 1)
            ->orderBy('color_name')
            ->get();
    }

    public function getActiveSizes()
    {
        return SizeModel::query()
            ->where('status', 1)
            ->orderBy('size_name')
            ->get();
    }

    public function getActiveTaxes()
    {
        return TaxModel::query()
            ->where('status', 1)
            ->orderBy('tax_name')
            ->get();
    }

    public function getActiveCoupons()
    {
        return CouponModel::query()
            ->where('status', 1)
            ->orderBy('coupon_code')
            ->get();
    }

    public function toggle_status($status, $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return ['status' => false, 'message' => 'Product not found.'];
        }

        $product->status = $status;

        if ($product->save()) {
            return ['status' => true, 'message' => 'Product status updated successfully.'];
        } else {
            return ['status' => false, 'message' => 'Failed to update product status.'];
        }
    }


}