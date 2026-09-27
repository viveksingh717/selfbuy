<?php

namespace App\Http\ViewComposers;

use App\Models\Category;
use App\Services\StorefrontCacheService;
use Illuminate\View\View;

class MobileMenuComposer
{
    public function compose(View $view): void
    {
        // Same query as the header menu - shares its cache entry.
        $mobileCategories = app(StorefrontCacheService::class)->remember('nav:categories', fn () => Category::where('status', 1)
            ->orderBy('category_name')
            ->with('subcategories')
            ->get());

        $view->with('mobileCategories', $mobileCategories);
    }
}
