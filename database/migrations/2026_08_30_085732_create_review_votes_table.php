<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            // Guest-or-user ownership, same pattern as CartService/WishlistService
            // elsewhere in this app — one vote per person is enforced at the
            // application level (ReviewVoteController), not via a DB unique
            // index, since a composite index can't reliably enforce that once
            // one of the two ownership columns is nullable.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->boolean('is_helpful');
            $table->timestamps();

            $table->index(['review_id', 'user_id']);
            $table->index(['review_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_votes');
    }
};
