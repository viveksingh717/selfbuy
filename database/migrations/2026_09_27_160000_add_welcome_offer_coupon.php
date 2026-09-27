<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * First-time account sign-up gets a personal single-use welcome coupon. Rules live in
     * Admin > Home Settings > Sign Up Offer (defaults mirror the WELCOME10 coupon); the issued
     * coupon is linked to the user so it is only ever given once.
     */
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->boolean('signup_offer_coupon_enabled')->default(true);
            $table->string('signup_offer_percent')->nullable()->default('10');
            $table->string('signup_offer_max_discount')->nullable()->default('200');
            $table->string('signup_offer_min_order')->nullable()->default('500');
            $table->string('signup_offer_valid_days')->nullable()->default('30');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('welcome_coupon_id')->nullable()
                ->constrained('coupon_models')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('welcome_coupon_id');
        });

        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'signup_offer_coupon_enabled',
                'signup_offer_percent',
                'signup_offer_max_discount',
                'signup_offer_min_order',
                'signup_offer_valid_days',
            ]);
        });
    }
};
