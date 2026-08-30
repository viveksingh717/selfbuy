<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Structured fields for the "My Account > Address" tab. The existing
     * `address` column is a single free-text field that's never actually been
     * used anywhere in the app — these mirror the checkout form's own address
     * fields instead, so a saved address can prefill checkout directly.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_line1')->nullable()->after('address');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country']);
        });
    }
};
