<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_models', function (Blueprint $table) {
            $table->id();
             // ── Basic Info ──────
            $table->string('product_name');
            $table->string('product_slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('additional_description')->nullable();

            // ── Classification ────────────────────────────
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();

            // ── Pricing ───────────────────────────────────
            $table->decimal('original_price', 10, 2)->default(0);   // MRP
            $table->decimal('selling_price', 10, 2)->default(0);    // actual selling
            $table->decimal('cost_price', 10, 2)->default(0);       // internal cost
            $table->decimal('discount', 5, 2)->default(0);          // discount %
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();

            // ── Inventory ─────────────────────────────────
            $table->string('sku')->unique()->nullable();
            $table->unsignedInteger('qty')->default(0);
            $table->unsignedInteger('low_stock_alert')->default(0);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'pre_order'])
                  ->default('in_stock');

            // ── Images ────────────────────────────────────
            $table->string('product_image')->nullable();            // thumbnail

            // ── Flags ─────────────────────────────────────
            $table->tinyInteger('status')->default(1);      // 0 = inactive, 1 = active
            $table->tinyInteger('is_featured')->default(0);   // 0 = not featured, 1 = featured
            $table->tinyInteger('is_trending')->default(0);   // 0 = not trending, 1 = trending

            // ── SEO ───────────────────────────────────────
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // ── Foreign Keys ──────────────────────────────
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('tax_id')->references('id')->on('tax_models')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupon_models')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();  // allows trash/restore instead of hard delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_models');
    }
};
