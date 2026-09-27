<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Single-row table - one column per home page setting. There is only ever
     * one row (id = 1); read it from views with home_setting('column_name').
     * Carousel slides live in their own home_slides table (any number of slides).
     */
    public function up(): void
    {
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();

            // ── Section headings ─────────────────────────────────
            $table->string('trendy_products_title')->nullable();
            $table->string('shop_by_category_title')->nullable();
            $table->string('new_arrivals_title')->nullable();

            // ── Sign up offer ────────────────────────────────────
            $table->boolean('signup_offer_enabled')->default(true);
            $table->string('signup_offer_title')->nullable();
            $table->text('signup_offer_text')->nullable();
            $table->string('signup_offer_button_text')->nullable();
            $table->string('signup_offer_button_link')->nullable();
            $table->string('signup_offer_background')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
