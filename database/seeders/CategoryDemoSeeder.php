<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name'     => 'Electronics',
                'category_slug'     => 'electronics',
                'description'       => 'Discover the latest gadgets, smartphones, laptops, TVs, and all your favourite electronic products.',
                'category_image'    => 'cat_electronics.jpg',
                'is_featured'       => 1,
                'status'            => 1,
                'meta_title'        => 'Electronics - Shop Latest Gadgets & Devices',
                'meta_description'  => 'Browse top electronics including smartphones, laptops, tablets, headphones, and more at the best prices.',
            ],
            [
                'category_name'     => 'Fashion & Clothing',
                'category_slug'     => 'fashion-clothing',
                'description'       => 'Explore the latest fashion trends for men, women, and kids — from everyday casuals to premium styles.',
                'category_image'    => 'cat_fashion.jpg',
                'is_featured'       => 1,
                'status'            => 1,
                'meta_title'        => 'Fashion & Clothing - Trendy Outfits for Everyone',
                'meta_description'  => 'Shop men, women, and kids fashion. Discover clothing, shoes, accessories, and more.',
            ],
            [
                'category_name'     => 'Home & Living',
                'category_slug'     => 'home-living',
                'description'       => 'Transform your home with stylish furniture, decor, kitchenware, and everything you need for comfortable living.',
                'category_image'    => 'cat_home.jpg',
                'is_featured'       => 1,
                'status'            => 1,
                'meta_title'        => 'Home & Living - Furniture, Decor & More',
                'meta_description'  => 'Find the perfect furniture, home decor, kitchen essentials, and bedding to create your dream home.',
            ],
            [
                'category_name'     => 'Sports & Outdoors',
                'category_slug'     => 'sports-outdoors',
                'description'       => 'Get the best sports equipment, outdoor gear, and fitness accessories to fuel your active lifestyle.',
                'category_image'    => 'cat_sports.jpg',
                'is_featured'       => 1,
                'status'            => 1,
                'meta_title'        => 'Sports & Outdoors - Gear Up for Every Sport',
                'meta_description'  => 'Shop sports equipment, gym gear, cricket, football, outdoor adventure gear, and more.',
            ],
            [
                'category_name'     => 'Beauty & Personal Care',
                'category_slug'     => 'beauty-personal-care',
                'description'       => 'Look and feel your best with premium skincare, makeup, hair care, fragrances, and personal care products.',
                'category_image'    => 'cat_beauty.jpg',
                'is_featured'       => 1,
                'status'            => 1,
                'meta_title'        => 'Beauty & Personal Care - Skincare, Makeup & More',
                'meta_description'  => 'Explore skincare, makeup, hair care, fragrances, and wellness products from top brands.',
            ],
            [
                'category_name'     => 'Books & Education',
                'category_slug'     => 'books-education',
                'description'       => 'Expand your knowledge with our wide selection of books — from bestselling fiction to academic and self-help titles.',
                'category_image'    => 'cat_books.jpg',
                'is_featured'       => 0,
                'status'            => 1,
                'meta_title'        => 'Books & Education - Fiction, Academic & Self-Help',
                'meta_description'  => 'Browse thousands of books including fiction, non-fiction, academic, children\'s books, and more.',
            ],
            [
                'category_name'     => 'Toys & Games',
                'category_slug'     => 'toys-games',
                'description'       => 'Find the perfect toys, board games, and educational activities for kids of all ages.',
                'category_image'    => 'cat_toys.jpg',
                'is_featured'       => 0,
                'status'            => 1,
                'meta_title'        => 'Toys & Games - Fun for Kids of All Ages',
                'meta_description'  => 'Shop action figures, board games, educational toys, remote control cars, and more for kids.',
            ],
            [
                'category_name'     => 'Food & Grocery',
                'category_slug'     => 'food-grocery',
                'description'       => 'Order fresh groceries, snacks, beverages, and organic food products delivered right to your door.',
                'category_image'    => 'cat_food.jpg',
                'is_featured'       => 0,
                'status'            => 1,
                'meta_title'        => 'Food & Grocery - Fresh, Organic & Packaged Foods',
                'meta_description'  => 'Order fresh fruits, vegetables, dairy, snacks, beverages, and organic products online.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['category_slug' => $category['category_slug']],
                $category
            );
        }

        $this->command->info('Categories seeded: ' . count($categories));
    }
}
