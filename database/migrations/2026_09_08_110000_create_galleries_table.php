<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Image gallery managed from Admin > Gallery. Each row is one image with
     * an optional title / caption / album grouping, for use anywhere on the
     * storefront (about page, a dedicated gallery page, home strip, ...).
     */
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('album')->nullable()->index();   // free-text grouping e.g. "Store", "Events"
            $table->text('caption')->nullable();
            $table->string('image');                        // filename on the "public" disk (gallery/)
            $table->string('link_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
