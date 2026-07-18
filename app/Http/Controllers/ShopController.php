<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    private $categoryService;
    private $productService;

    public function __construct() {
        $this->categoryService = new CategoryService();
        $this->productService = new ProductService();
    }

    public function index(Request $request, $slug, $slug2 = '')
    {
        $category = $this->categoryService->getCategoryBySlug($slug);

        if (empty($category)) {
            abort(404);
        }

        $subcategory = null;

        if (!empty($slug2)) {
            $subcategory = $this->categoryService->getSubCategoryBySlug($slug2);

            if (empty($subcategory) || $subcategory->category_id != $category->id) {
                abort(404);
            }
        }

        $filters = $this->extractFilters($request);

        $products = $this->productService->getProductsByCategory(
            $category->id,
            $subcategory->id ?? null,
            $filters
        );

        $data = $this->filterWidgetData($filters);
        $data['category'] = $category;
        $data['subcategory'] = $subcategory;
        $data['products'] = $products;
        $data['subcategories'] = $this->categoryService->getSubCategoriesWithProductCounts($category->id);

        return view('shop.products', $data);
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        $filters = $this->extractFilters($request);

        $products = $term !== ''
            ? $this->productService->searchProducts($term, $filters)
            : $this->productService->emptyProductPaginator();

        $data = $this->filterWidgetData($filters);
        $data['term'] = $term;
        $data['products'] = $products;

        return view('shop.search', $data);
    }

    public function productDetails($slug)
    {
        $product = $this->productService->getProductBySlug($slug);

        if (empty($product)) {
            abort(404);
        }

        $data['product'] = $product;
        $data['relatedProducts'] = $this->productService->getRelatedProducts($product);

        return view('shop.product_details', $data);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'brand_ids' => array_filter((array) $request->input('brand_id', [])),
            'color_ids' => array_filter((array) $request->input('color_id', [])),
            'size_ids' => array_filter((array) $request->input('size_id', [])),
            'min_price' => is_numeric($request->input('min_price')) ? $request->input('min_price') : null,
            'max_price' => is_numeric($request->input('max_price')) ? $request->input('max_price') : null,
            'sort' => $request->input('sort', 'latest'),
        ];
    }

    private function filterWidgetData(array $filters): array
    {
        return [
            'brands' => $this->productService->getActiveBrands(),
            'colors' => $this->productService->getActiveColors(),
            'sizes' => $this->productService->getActiveSizes(),
            'selectedBrandIds' => $filters['brand_ids'],
            'selectedColorIds' => $filters['color_ids'],
            'selectedSizeIds' => $filters['size_ids'],
            'minPrice' => $filters['min_price'],
            'maxPrice' => $filters['max_price'],
            'sort' => $filters['sort'],
        ];
    }
}
