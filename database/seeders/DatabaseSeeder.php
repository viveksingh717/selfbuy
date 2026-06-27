<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(AdminUserSeeder::class);

        // Demo data
        $this->call(CategoryDemoSeeder::class);
        $this->call(SubCategoryDemoSeeder::class);
        $this->call(BrandDemoSeeder::class);
        $this->call(BannerDemoSeeder::class);
        $this->call(TaxDemoSeeder::class);
        $this->call(CouponDemoSeeder::class);
        $this->call(ProductDemoSeeder::class);
    }
}
