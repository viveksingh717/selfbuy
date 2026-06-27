<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'       => 'New Arrivals in Electronics',
                'subtitle'    => 'Upto 40% OFF',
                'description' => 'Shop the latest smartphones, laptops, and gadgets from top brands at unbeatable prices.',
                'image'       => 'banner_hero_1.jpg',
                'button_text' => 'Shop Now',
                'button_link' => '/electronics',
                'position'    => 'hero',
                'sort_order'  => 1,
                'status'      => 1,
            ],
            [
                'title'       => 'Fashion Sale — This Weekend Only',
                'subtitle'    => 'Flat 50% OFF on Clothing',
                'description' => 'Refresh your wardrobe with the latest styles for men, women, and kids.',
                'image'       => 'banner_hero_2.jpg',
                'button_text' => 'Explore Fashion',
                'button_link' => '/fashion-clothing',
                'position'    => 'hero',
                'sort_order'  => 2,
                'status'      => 1,
            ],
            [
                'title'       => 'Home Makeover Deals',
                'subtitle'    => 'Save Big on Furniture & Decor',
                'description' => 'Discover beautiful furniture, lighting, and home accessories to transform your space.',
                'image'       => 'banner_promo_1.jpg',
                'button_text' => 'Shop Home',
                'button_link' => '/home-living',
                'position'    => 'promo',
                'sort_order'  => 1,
                'status'      => 1,
            ],
            [
                'title'       => 'Sports Super Sale',
                'subtitle'    => 'Gear Up & Save Up to 30%',
                'description' => 'Everything you need for an active lifestyle — gym, cricket, football, yoga and more.',
                'image'       => 'banner_promo_2.jpg',
                'button_text' => 'Shop Sports',
                'button_link' => '/sports-outdoors',
                'position'    => 'promo',
                'sort_order'  => 2,
                'status'      => 1,
            ],
            [
                'title'       => 'Beauty Essentials',
                'subtitle'    => 'Look Your Best Every Day',
                'description' => 'Premium skincare, makeup, and personal care products from top brands.',
                'image'       => 'banner_sidebar.jpg',
                'button_text' => 'Shop Beauty',
                'button_link' => '/beauty-personal-care',
                'position'    => 'sidebar',
                'sort_order'  => 1,
                'status'      => 1,
            ],
        ];

        Banner::truncate();
        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        $this->command->info('Banners seeded: ' . count($banners));
    }
}
