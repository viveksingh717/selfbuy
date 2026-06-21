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
        Schema::table('tax_models', function (Blueprint $table) {

            // ── fix existing columns ──────────────────────

            // tax_name: remove unique (same tax name can exist with diff types e.g. GST 5%, GST 18%)
            $table->string('tax_name')->nullable()->change();

            // tax_type: remove unique — multiple taxes can be 'percentage'
            // change to enum with proper values
            $table->enum('tax_type', ['percentage', 'fixed', 'compound', 'inclusive'])
                  ->default('percentage')->nullable(false)->change();

            // status: change boolean to tinyInteger for consistency
            $table->tinyInteger('status')->default(1)->change();

            // ── add missing columns ───────────────────────
            $table->string('tax_alias', 20)->nullable()->after('tax_name');
            $table->decimal('min_order_amount', 10, 2)->default(0)->after('tax_rate');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('min_order_amount');
            $table->enum('applicable_to', ['all', 'category', 'product'])->default('all')->after('max_discount_amount');
            $table->enum('tax_region', ['all', 'domestic', 'international'])->default('all')->after('applicable_to');
            $table->unsignedInteger('priority')->default(1)->after('tax_region');
            $table->text('description')->nullable()->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_models', function (Blueprint $table) {

            // remove added columns
            $table->dropColumn([
                'tax_alias',
                'min_order_amount',
                'max_discount_amount',
                'applicable_to',
                'tax_region',
                'priority',
                'description',
            ]);

            // revert changed columns
            $table->string('tax_name')->unique()->change();
            $table->string('tax_type')->unique()->nullable()->change();
            $table->boolean('status')->default(true)->change();
        });
    }
};
