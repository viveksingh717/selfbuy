<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One account per phone number. Existing data is cleaned first, because the
     * unique index can't be added while duplicates exist:
     *   1. numbers are stored unformatted ("98765 43210" -> "9876543210"), blank -> null
     *   2. a number shared by several accounts stays on the oldest account (lowest id)
     *      and is cleared on the others - those users can add a new number in My Account
     */
    public function up(): void
    {
        DB::table('users')->whereNotNull('phone_number')->orderBy('id')
            ->each(function ($user) {
                $normalized = User::normalizePhone($user->phone_number);
                if ($normalized !== $user->phone_number) {
                    DB::table('users')->where('id', $user->id)->update(['phone_number' => $normalized]);
                }
            });

        $duplicates = DB::table('users')
            ->select('phone_number')
            ->whereNotNull('phone_number')
            ->groupBy('phone_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone_number');

        foreach ($duplicates as $phone) {
            $keepId = DB::table('users')->where('phone_number', $phone)->min('id');
            DB::table('users')->where('phone_number', $phone)->where('id', '!=', $keepId)
                ->update(['phone_number' => null]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone_number');
        });
    }

    /**
     * Reverse the migrations. Cleared duplicate numbers are not restored.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone_number']);
        });
    }
};
