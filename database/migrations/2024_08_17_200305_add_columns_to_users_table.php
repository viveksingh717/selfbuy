<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->after('password')->nullable();
            $table->text('address')->after('phone_number')->nullable();
            $table->string('profile_photo')->after('address')->nullable();
            $table->tinyInteger('status')->after('profile_photo')->default(1);
            $table->tinyInteger('role_type')->after('status')->default(1);
            $table->tinyInteger('is_verified')->after('role_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'address', 'profile_photo', 'status', 'role_type', 'is_verified']);
        });
    }
};
