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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->string('sku_variant')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('extra_price', 8, 2)->default(0);
            $table->tinyInteger('status')->default(1);

            $table->foreign('product_id')
                  ->references('id')->on('product_models')->onDelete('cascade');
            $table->foreign('color_id')
                  ->references('id')->on('color_models')->onDelete('set null');
            $table->foreign('size_id')
                  ->references('id')->on('size_models')->onDelete('set null');

            $table->unique(['product_id', 'color_id', 'size_id'], 'unique_product_variant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
