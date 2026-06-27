<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandDemoSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['brand_name' => 'Samsung',  'brand_slug' => 'samsung',  'brand_logo' => 'brand_samsung.jpg',  'status' => 1],
            ['brand_name' => 'Apple',    'brand_slug' => 'apple',    'brand_logo' => 'brand_apple.jpg',    'status' => 1],
            ['brand_name' => 'Nike',     'brand_slug' => 'nike',     'brand_logo' => 'brand_nike.jpg',     'status' => 1],
            ['brand_name' => 'Sony',     'brand_slug' => 'sony',     'brand_logo' => 'brand_sony.jpg',     'status' => 1],
            ['brand_name' => 'Adidas',   'brand_slug' => 'adidas',   'brand_logo' => 'brand_adidas.jpg',   'status' => 1],
            ['brand_name' => 'LG',       'brand_slug' => 'lg',       'brand_logo' => 'brand_lg.jpg',       'status' => 1],
            ['brand_name' => 'Philips',  'brand_slug' => 'philips',  'brand_logo' => 'brand_philips.jpg',  'status' => 1],
            ['brand_name' => 'Puma',     'brand_slug' => 'puma',     'brand_logo' => 'brand_puma.jpg',     'status' => 1],
            ['brand_name' => 'Dell',     'brand_slug' => 'dell',     'brand_logo' => 'brand_dell.jpg',     'status' => 1],
            ['brand_name' => 'HP',       'brand_slug' => 'hp',       'brand_logo' => 'brand_hp.jpg',       'status' => 1],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['brand_slug' => $brand['brand_slug']], $brand);
        }

        $this->command->info('Brands seeded: ' . count($brands));
    }
}
