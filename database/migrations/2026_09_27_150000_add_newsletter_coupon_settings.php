<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Newsletter sign-ups get their own single-use coupon. The coupon rules live in
     * Admin > Home Settings > Newsletter Popup; the issued coupon is linked to the
     * subscriber so re-subscribing never hands out a second one.
     */
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->boolean('newsletter_coupon_enabled')->default(true);
            $table->string('newsletter_coupon_max_discount')->nullable()->default('500');
            $table->string('newsletter_coupon_min_order')->nullable()->default('999');
            $table->string('newsletter_coupon_valid_days')->nullable()->default('30');
        });

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('user_id')
                ->constrained('coupon_models')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
        });

        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'newsletter_coupon_enabled',
                'newsletter_coupon_max_discount',
                'newsletter_coupon_min_order',
                'newsletter_coupon_valid_days',
            ]);
        });
    }
};
