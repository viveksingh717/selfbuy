<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Home page carousel - one row per slide, managed from Admin > Home Settings > Carousel.
     */
    public function up(): void
    {
        Schema::create('home_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('image_mobile')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Start with the theme's current slides so the home page looks the same until the admin edits it.
        // Images starting with "assets/" are bundled theme files (public/), uploads live in storage.
        $slides = [
            1 => ['Topsale Collection',   "Living Room\nFurniture"],
            2 => ['News and Inspiration', 'New Arrivals'],
            3 => ['Outdoor Furniture',    "Outdoor Dining\nFurniture"],
        ];

        foreach ($slides as $i => [$subtitle, $title]) {
            DB::table('home_slides')->insert([
                'image'        => "assets/images/slider/slide-{$i}.jpg",
                'image_mobile' => "assets/images/slider/slide-{$i}-480w.jpg",
                'subtitle'     => $subtitle,
                'title'        => $title,
                'button_text'  => 'SHOP NOW',
                'button_link'  => '#',
                'sort_order'   => $i,
                'status'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_slides');
    }
};
