<?php

namespace App\Http\ViewComposers;

use App\Models\Category;
use Illuminate\View\View;

class HeaderComposer
{
    public function compose(View $view): void
    {
        $headerCategories = Category::where('status', 1)
            ->orderBy('category_name')
            ->with('subcategories')
            ->get();

        $view->with('headerCategories', $headerCategories);
    }
}
