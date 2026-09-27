<?php

namespace App\Http\Controllers;

use App\Models\PageSetting;
use App\Models\Order;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\StorefrontCacheService;
use App\Services\WishlistService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    // ── Customer-service pages: content managed in Admin > Pages (page_settings) ──
    public function payment()
    {
        return $this->cmsPage('payment-method');
    }

    public function money_back_guarantee()
    {
        return $this->cmsPage('money-back-guarantee');
    }

    public function refund_policy()
    {
        return $this->cmsPage('refund-policy');
    }

    public function shipping()
    {
        return $this->cmsPage('shipping');
    }

    public function terms_and_conditions()
    {
        return $this->cmsPage('terms-and-conditions');
    }

    public function privacy_policy()
    {
        return $this->cmsPage('privacy-policy');
    }

    /** One template for all of them; a page switched off in admin is a 404. */
    private function cmsPage(string $slug)
    {
        $page = PageSetting::where('slug', $slug)->where('status', 1)->firstOrFail();

        return view('partials.cms_page', compact('page'));
    }

    /** Track My Order - signed-in customers only, and only their own orders. */
    public function track_my_order(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            // Come back here (same ?order=) once the sign-in / OTP step completes.
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('home')->with([
                'open_auth_modal' => 'signin',
                'error'           => 'Please sign in to track your orders.',
            ]);
        }

        $orders = Order::with(['items', 'histories'])
            ->where('user_id', Auth::guard('web')->id())
            ->latest()
            ->get();

        // ?order=... if it's one of theirs, else the latest order still on its way, else the latest order.
        $selected = $orders->firstWhere('order_number', $request->query('order'))
            ?? $orders->first(fn ($o) => !in_array($o->order_status, ['delivered', 'cancelled'], true))
            ?? $orders->first();

        return view('partials.track_my_order', compact('orders', 'selected'));
    }

    public function blog()
    {
        return view('partials.blog');
    }

}
