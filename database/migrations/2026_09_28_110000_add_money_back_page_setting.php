<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The footer's "Money-back Guarantee" page had no row in Admin > Pages (page_settings),
     * so it couldn't be edited like the other customer-service pages. Add it.
     */
    public function up(): void
    {
        if (!DB::table('page_settings')->where('slug', 'money-back-guarantee')->exists()) {
            DB::table('page_settings')->insert([
                'slug'              => 'money-back-guarantee',
                'name'              => 'Money-back Guarantee',
                'label'             => 'Money-back Guarantee',
                'title'             => 'Money-back Guarantee',
                'short_description' => 'Shop with confidence - if something is not right, we will make it right.',
                'status'            => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('page_settings')->where('slug', 'money-back-guarantee')->delete();
    }
};
