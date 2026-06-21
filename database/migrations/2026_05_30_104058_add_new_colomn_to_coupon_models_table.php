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
        Schema::table('coupon_models', function (Blueprint $table) {
            $table->string('coupon_name')->nullable()->change();
            $table->enum('discount_type', ['percentage', 'fixed_cart', 'free_shipping', 'buy_x_get_y'])
                  ->default('percentage')->change();

            // rename discount_amount → discount_value
            $table->renameColumn('discount_amount', 'discount_value');

            // add all new columns
            $table->text('description')->nullable()->after('coupon_code');
            $table->decimal('min_order_amount', 10, 2)->default(0)->after('discount_value');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('min_order_amount');
            $table->date('start_date')->nullable()->after('max_discount_amount');
            $table->unsignedInteger('usage_limit')->nullable()->after('expiry_date');
            $table->unsignedInteger('used_count')->default(0)->after('usage_limit');
            $table->unsignedInteger('per_user_limit')->nullable()->after('used_count');
            $table->enum('applicable_to', ['all', 'category', 'product'])->default('all')->after('per_user_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_models', function (Blueprint $table) {
            $table->renameColumn('discount_value', 'discount_amount');
            $table->dropColumn([
                'description', 'min_order_amount', 'max_discount_amount',
                'start_date', 'usage_limit', 'used_count',
                'per_user_limit', 'applicable_to', 'status',
            ]);
        });
    }
};
