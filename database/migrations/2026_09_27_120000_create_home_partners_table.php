<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Partner logos shown below the home page carousel - one row per logo,
     * managed from Admin > Home Settings > Partners.
     */
    public function up(): void
    {
        Schema::create('home_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Start with the theme's current logos so the home page looks the same until the admin edits it.
        // Images starting with "assets/" are bundled theme files (public/), uploads live in storage.
        foreach (range(1, 6) as $i) {
            DB::table('home_partners')->insert([
                'name'       => "Partner {$i}",
                'image'      => "assets/images/brands/{$i}.png",
                'link'       => '#',
                'sort_order' => $i,
                'status'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_partners');
    }
};
