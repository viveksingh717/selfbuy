<?php

namespace App\Http\ViewComposers;

use App\Models\Category;
use Illuminate\View\View;

class MobileMenuComposer
{
    public function compose(View $view): void
    {
        $mobileCategories = Category::where('status', 1)
            ->orderBy('category_name')
            ->with('subcategories')
            ->get();

        $view->with('mobileCategories', $mobileCategories);
    }
}
