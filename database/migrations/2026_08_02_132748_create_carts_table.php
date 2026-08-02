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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();              // null = guest
            $table->string('session_id')->nullable();                       // guest identity
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_attribute_id')->nullable(); // color+size variant
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 10, 2);                           // selling_price snapshot
            $table->decimal('extra_price', 8, 2)->default(0);               // variant extra_price snapshot
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product_models')->onDelete('cascade');
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');

            $table->index('user_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
