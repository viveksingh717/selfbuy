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

        $selectedBrandIds = array_filter((array) $request->input('brand_id', []));
        $selectedColorIds = array_filter((array) $request->input('color_id', []));
        $selectedSizeIds = array_filter((array) $request->input('size_id', []));
        $minPrice = is_numeric($request->input('min_price')) ? $request->input('min_price') : null;
        $maxPrice = is_numeric($request->input('max_price')) ? $request->input('max_price') : null;
        $sort = $request->input('sort', 'latest');

        $products = $this->productService->getProductsByCategory(
            $category->id,
            $subcategory->id ?? null,
            [
                'brand_ids' => $selectedBrandIds,
                'color_ids' => $selectedColorIds,
                'size_ids' => $selectedSizeIds,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sort,
            ]
        );

        $data['category'] = $category;
        $data['subcategory'] = $subcategory;
        $data['products'] = $products;
        $data['subcategories'] = $this->categoryService->getSubCategoriesWithProductCounts($category->id);
        $data['brands'] = $this->productService->getActiveBrands();
        $data['colors'] = $this->productService->getActiveColors();
        $data['sizes'] = $this->productService->getActiveSizes();
        $data['selectedBrandIds'] = $selectedBrandIds;
        $data['selectedColorIds'] = $selectedColorIds;
        $data['selectedSizeIds'] = $selectedSizeIds;
        $data['minPrice'] = $minPrice;
        $data['maxPrice'] = $maxPrice;
        $data['sort'] = $sort;

        return view('shop.products', $data);
    }
}
