<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One flat table for every static storefront page (About Us, Contact Us,
     * FAQ, Payment Method, Shipping, Refund Policy, Privacy Policy, Terms ...).
     * Everything except the slug is optional - the front-end fetches a row by
     * slug and prints whichever fields are filled in.
     */
    public function up(): void
    {
        Schema::create('page_settings', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();               // lookup key, e.g. "about-us"
            $table->string('name')->nullable();             // "About Us" (admin list label)
            $table->string('label')->nullable();            // menu / footer link text
            $table->string('title')->nullable();            // heading shown on the page

            $table->text('short_description')->nullable();   // small intro text / HTML
            $table->longText('description')->nullable();     // main body text / HTML
            $table->longText('content')->nullable();         // extra content block, if needed

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->tinyInteger('status')->default(1);       // 1 = active, 0 = inactive
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_settings');
    }
};
