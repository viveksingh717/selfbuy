<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store-wide admin notifications (new contact message, new order, ...).
     * Not tied to a specific admin user - the whole team shares one feed.
     */
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('general')->index();  // contact | order | general ...
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('url')->nullable();                     // where clicking it goes
            $table->string('icon')->default('fa-bell');
            $table->json('data')->nullable();                      // arbitrary payload (e.g. related id)
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
