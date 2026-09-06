<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        if (Gallery::exists()) {
            return;
        }

        $seed = [
            ['title' => 'Our storefront',      'album' => 'Store'],
            ['title' => 'Packing station',     'album' => 'Store'],
            ['title' => 'Warehouse aisle',     'album' => 'Warehouse'],
            ['title' => 'Launch day 2026',     'album' => 'Events'],
            ['title' => 'Team offsite',        'album' => 'Events'],
            ['title' => 'Customer unboxing',   'album' => 'Community'],
        ];

        foreach ($seed as $i => $row) {
            Gallery::create($row + [
                'image'      => 'assets/images/portfolio/item-' . ($i + 1) . '.jpg',
                'sort_order' => $i + 1,
                'status'     => 1,
            ]);
        }
    }
}
