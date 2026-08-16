<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();

            // Gateway-agnostic by design: 'razorpay' today, 'stripe' / 'paypal' / 'upi' later,
            // without needing new tables or columns per provider.
            $table->string('gateway');
            $table->string('gateway_order_id')->nullable()->unique();
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_signature')->nullable();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('created'); // created, paid, failed, cancelled

            // Billing details AND a full cart snapshot (items + totals breakdown),
            // captured at checkout before payment completes. The order is built from
            // this snapshot rather than the live cart, because the request that
            // completes the payment (a webhook, in particular) may have no session
            // or cart context of its own at all.
            $table->json('billing_data')->nullable();
            $table->json('order_snapshot')->nullable();
            $table->json('meta')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('session_id');
            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
