<?php

namespace App\Http\ViewComposers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductModel;
use App\Services\StorefrontCacheService;
use Illuminate\View\View;

class HeaderComposer
{
    public function compose(View $view): void
    {
        $cache = app(StorefrontCacheService::class);

        $headerCategories = $cache->remember('nav:categories', fn () => Category::where('status', 1)
            ->orderBy('category_name')
            ->with('subcategories')
            ->get());

        // "Product" menu > Shop by Brand: active brands that have active products, busiest first.
        $headerBrands = $cache->remember('nav:brands', function () {
            $brandCounts = ProductModel::where('status', 1)
                ->whereNotNull('brand_id')
                ->selectRaw('brand_id, COUNT(*) as total')
                ->groupBy('brand_id')
                ->pluck('total', 'brand_id');

            return Brand::where('status', 1)
                ->whereIn('id', $brandCounts->keys())
                ->get(['id', 'brand_name'])
                ->sortBy([fn ($a, $b) => $brandCounts[$b->id] <=> $brandCounts[$a->id], ['brand_name', 'asc']])
                ->take(8)
                ->values();
        });

        $view->with([
            'headerCategories' => $headerCategories,
            'headerBrands'     => $headerBrands,
        ]);
    }
}
