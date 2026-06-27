<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'electronics' => [
                ['Smartphones',  'smartphones'],
                ['Laptops',      'laptops'],
                ['Tablets',      'tablets'],
                ['Headphones',   'headphones'],
                ['Cameras',      'cameras'],
                ['Smart TVs',    'smart-tvs'],
            ],
            'fashion-clothing' => [
                ["Men's Wear",   'mens-wear'],
                ["Women's Wear", 'womens-wear'],
                ["Kids' Wear",   'kids-wear'],
                ['Shoes',        'shoes'],
                ['Accessories',  'accessories'],
                ['Watches',      'watches'],
            ],
            'home-living' => [
                ['Furniture',        'furniture'],
                ['Kitchen & Dining', 'kitchen-dining'],
                ['Bedding',          'bedding'],
                ['Home Decor',       'home-decor'],
                ['Lighting',         'lighting'],
            ],
            'sports-outdoors' => [
                ['Gym Equipment',  'gym-equipment'],
                ['Cricket',        'cricket'],
                ['Football',       'football'],
                ['Swimming',       'swimming'],
                ['Yoga & Fitness', 'yoga-fitness'],
            ],
            'beauty-personal-care' => [
                ['Skincare',     'skincare'],
                ['Makeup',       'makeup'],
                ['Hair Care',    'hair-care'],
                ['Fragrances',   'fragrances'],
                ['Personal Care','personal-care'],
            ],
            'books-education' => [
                ['Fiction',          'fiction'],
                ['Academic Books',   'academic-books'],
                ['Self Help',        'self-help'],
                ["Children's Books", 'childrens-books'],
                ['Comics & Manga',   'comics-manga'],
            ],
            'toys-games' => [
                ['Action Figures',    'action-figures'],
                ['Board Games',       'board-games'],
                ['Educational Toys',  'educational-toys'],
                ['Remote Control',    'remote-control'],
                ['Dolls & Playsets',  'dolls-playsets'],
            ],
            'food-grocery' => [
                ['Fresh Fruits & Vegetables', 'fresh-fruits-vegetables'],
                ['Dairy Products',            'dairy-products'],
                ['Snacks & Namkeen',          'snacks-namkeen'],
                ['Beverages',                 'beverages'],
                ['Organic Foods',             'organic-foods'],
            ],
        ];

        $count = 0;
        foreach ($map as $categorySlug => $subcategories) {
            $category = Category::where('category_slug', $categorySlug)->first();
            if (!$category) continue;

            foreach ($subcategories as [$name, $slug]) {
                SubCategory::updateOrCreate(
                    ['subcategory_slug' => $slug],
                    [
                        'subcategory_name'  => $name,
                        'subcategory_slug'  => $slug,
                        'subcategory_image' => null,
                        'category_id'       => $category->id,
                        'status'            => 1,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("SubCategories seeded: {$count}");
    }
}
