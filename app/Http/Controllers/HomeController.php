<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\StorefrontCacheService;
use App\Services\WishlistService;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index(ProductService $productService, CategoryService $categoryService, WishlistService $wishlistService, StorefrontCacheService $cache)
    {
        // Trendy Products = products marked "Trending" in admin.
        $trendyProducts = $cache->remember('home:trending', fn () => $productService->getTrendingProducts(12));

        // New Arrivals = latest active products; "All" shows the newest 8.
        $newArrivals = $cache->remember('home:new-arrivals', fn () => $productService->getNewArrivals(8));

        return view('selfbuy', [
            'trendyProducts'       => $trendyProducts,
            'trendyCategories'     => $this->tabCategories($trendyProducts),
            'homeCategories'       => $cache->remember('home:categories', fn () => $categoryService->getHomeCategories()),
            'newArrivals'          => $newArrivals,
            'newArrivalCategories' => $this->tabCategories($newArrivals),
            'wishlistedProductIds' => $wishlistService->getWishlistedProductIds(),
        ]);
    }

    /**
     * Category tabs for a product section: the (up to 4) categories with the most
     * products in it, so a category only gets a tab when it has products to show.
     */
    private function tabCategories(Collection $products, int $limit = 4): Collection
    {
        return $products
            ->groupBy('category_id')
            ->filter(fn ($group) => $group->first()->category)
            ->sortByDesc(fn ($group) => $group->count())
            ->take($limit)
            ->map(fn ($group) => $group->first()->category);
    }

    public function about()
    {
        return view('partials.about_us');
    }

    public function faq()
    {
        return view('partials.faq');
    }

    public function contact()
    {
        return view('partials.contact');
    }

    public function payment()
    {
        return view('partials.payment');
    }

    public function money_back_guarantee()
    {
        return view('partials.money_back');
    }

    public function refund_policy()
    {
        return view('partials.refund_policy');
    }

    public function shipping()
    {
        return view('partials.shipping');
    }

    public function terms_and_conditions()
    {
        return view('partials.terms_condition');
    }

    public function privacy_policy()
    {
        return view('partials.privacy_policy');
    }

    public function track_my_order()
    {
        return view('partials.track_my_order');
    }

    public function blog()
    {
        return view('partials.blog');
    }

}
