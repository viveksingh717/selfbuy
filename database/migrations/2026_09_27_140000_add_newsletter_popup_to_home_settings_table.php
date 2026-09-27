<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Newsletter popup content (Admin > Home Settings > Newsletter Popup).
     * Column defaults match the schema defaults so the existing settings row
     * keeps showing the current popup text.
     */
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->boolean('newsletter_popup_enabled')->default(true);
            $table->string('newsletter_popup_delay')->nullable()->default('5');
            $table->string('newsletter_popup_offer_prefix')->nullable()->default('get');
            $table->string('newsletter_popup_offer_percent')->nullable()->default('25');
            $table->string('newsletter_popup_offer_text')->nullable()->default('off');
            $table->string('newsletter_popup_image')->nullable();
            $table->string('newsletter_popup_description', 1000)->nullable()
                ->default('Subscribe to the SelfBuy eCommerce newsletter to receive timely updates from your favorite products.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'newsletter_popup_enabled',
                'newsletter_popup_delay',
                'newsletter_popup_offer_prefix',
                'newsletter_popup_offer_percent',
                'newsletter_popup_offer_text',
                'newsletter_popup_image',
                'newsletter_popup_description',
            ]);
        });
    }
};
