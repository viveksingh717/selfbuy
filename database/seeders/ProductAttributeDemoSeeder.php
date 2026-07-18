<?php

namespace Database\Seeders;

use App\Models\ColorModel;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use App\Models\SizeModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeDemoSeeder extends Seeder
{
    public function run(): void
    {
        $colorIds = ColorModel::where('status', 1)->pluck('id');
        $sizeIds = SizeModel::where('status', 1)->pluck('id');

        if ($colorIds->isEmpty() || $sizeIds->isEmpty()) {
            return;
        }

        ProductModel::all(['id', 'sku'])->each(function (ProductModel $product) use ($colorIds, $sizeIds) {
            foreach ($colorIds as $colorId) {
                ProductAttribute::create([
                    'product_id'  => $product->id,
                    'color_id'    => $colorId,
                    'size_id'     => $sizeIds->random(),
                    'stock'       => fake()->numberBetween(0, 50),
                    'extra_price' => fake()->randomElement([0, 0, 0, 100, 200, 500]),
                    'sku_variant' => $product->sku . '-' . Str::upper(Str::random(4)),
                    'status'      => 1,
                ]);
            }
        });
    }
}
