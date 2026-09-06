<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Meet Our Team" cards shown on the storefront About Us page,
     * managed from Admin > Team Members.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();      // "Founder & Owner"
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();            // filename on the "public" disk (team/)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
