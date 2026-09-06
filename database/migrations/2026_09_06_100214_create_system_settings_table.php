<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Single-row settings table - one column per setting. There is only ever
     * one row (id = 1); read it from anywhere with setting('column_name').
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // ── General ──────────────────────────────────────────
            $table->string('site_name')->nullable();
            $table->string('site_tagline')->nullable();
            $table->string('logo_horizontal')->nullable();
            $table->string('logo_light')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('favicon')->nullable();
            $table->string('admin_logo')->nullable();
            $table->boolean('maintenance_mode')->default(false);

            // ── Contact ──────────────────────────────────────────
            $table->string('contact_email')->nullable();
            $table->string('support_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_phone_alt')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->text('address_line')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_country')->nullable();
            $table->string('address_postcode')->nullable();
            $table->string('google_maps_url')->nullable();

            // ── Localization ─────────────────────────────────────
            $table->string('default_language')->default('en');
            $table->string('available_languages')->nullable();
            $table->string('default_currency')->default('INR');
            $table->string('available_currencies')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('default_country')->default('IN');
            $table->string('timezone')->nullable();

            // ── Payment ──────────────────────────────────────────
            $table->string('payment_image')->nullable();
            $table->string('payment_upi_icon')->nullable();
            $table->boolean('cod_enabled')->default(true);
            $table->text('payment_note')->nullable();

            // ── Footer ───────────────────────────────────────────
            $table->text('footer_about_text')->nullable();
            $table->string('footer_copyright')->nullable();
            $table->text('newsletter_text')->nullable();

            // ── Social ───────────────────────────────────────────
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('linkedin_url')->nullable();

            // ── Business hours ───────────────────────────────────
            $table->string('business_hours')->nullable();
            $table->string('support_hours')->nullable();
            $table->string('order_processing_time')->nullable();
            $table->string('delivery_time_estimate')->nullable();
            $table->string('order_cutoff_time')->nullable();

            // ── SEO ──────────────────────────────────────────────
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
