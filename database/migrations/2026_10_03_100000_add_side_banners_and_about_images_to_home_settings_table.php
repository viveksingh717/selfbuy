<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Home page side banners (Admin > Home Settings > Side Banners) and the two
     * About page photos (Admin > Home Settings > About Page). Column defaults match
     * the schema defaults so the existing settings row keeps showing the current text;
     * image columns stay empty so the bundled placeholder images are shown until an upload.
     */
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->string('side_banner_1_subtitle')->nullable()->default('Clearance');
            $table->string('side_banner_1_title', 500)->nullable()->default("Chairs & Chaises\nUp to 40% off");
            $table->string('side_banner_1_button_text')->nullable()->default('Shop Now');
            $table->string('side_banner_1_button_link')->nullable()->default('/products');
            $table->string('side_banner_1_image')->nullable();

            $table->string('side_banner_2_subtitle')->nullable()->default('New in');
            $table->string('side_banner_2_title', 500)->nullable()->default("Best Lighting\nCollection");
            $table->string('side_banner_2_button_text')->nullable()->default('Discover Now');
            $table->string('side_banner_2_button_link')->nullable()->default('/products');
            $table->string('side_banner_2_image')->nullable();

            $table->string('about_image_front')->nullable();
            $table->string('about_image_back')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'side_banner_1_subtitle',
                'side_banner_1_title',
                'side_banner_1_button_text',
                'side_banner_1_button_link',
                'side_banner_1_image',
                'side_banner_2_subtitle',
                'side_banner_2_title',
                'side_banner_2_button_text',
                'side_banner_2_button_link',
                'side_banner_2_image',
                'about_image_front',
                'about_image_back',
            ]);
        });
    }
};
