<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When a signed-in customer last loaded a page (updated at most once a minute by
     * TrackLastSeen). Powers "Active now" on the admin dashboard.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['last_seen_at']);
            $table->dropColumn('last_seen_at');
        });
    }
};
