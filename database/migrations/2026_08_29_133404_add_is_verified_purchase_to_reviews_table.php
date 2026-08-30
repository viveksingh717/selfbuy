<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Set once at submission time (see ReviewController::store()) by
            // checking whether the reviewer has an order containing this
            // product — a snapshot, not recomputed later, same rationale as
            // Payment::order_snapshot elsewhere in this app.
            $table->boolean('is_verified_purchase')->default(false)->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('is_verified_purchase');
        });
    }
};
