<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Admin order management: shipment tracking, status timestamps, and an activity
     * timeline (order_histories) recording every status / payment / tracking change.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_courier')->nullable()->after('order_status');
            $table->string('tracking_number')->nullable()->after('tracking_courier');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('tracking_url');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            $table->string('cancel_reason', 500)->nullable()->after('cancelled_at');
            $table->boolean('stock_restored')->default(false)->after('cancel_reason'); // guards against double restock
        });

        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('type', 20);                 // order | payment | tracking | email | note
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('customer_notified')->default(false);
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_histories');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_courier', 'tracking_number', 'tracking_url',
                'shipped_at', 'delivered_at', 'cancelled_at', 'cancel_reason', 'stock_restored',
            ]);
        });
    }
};
