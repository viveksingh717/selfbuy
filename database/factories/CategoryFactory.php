<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [

            'category_name' => ucwords($name),

            'category_slug' => Str::slug($name),

            'description' => fake()->paragraph(),

            'category_image' => fake()->imageUrl(640, 480, 'category'),

            'is_featured' => fake()->boolean(),

            'status' => fake()->boolean(),

            'meta_title' => fake()->sentence(6),

            'meta_description' => fake()->text(150),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}
