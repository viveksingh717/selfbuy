<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Services\SystemSettingService;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Ensures the single settings row exists and fills any still-empty column
     * with its schema default. Safe to re-run - it never overwrites a value
     * an admin has already set.
     */
    public function run(): void
    {
        $row = SystemSetting::instance();

        foreach (SystemSettingService::defaults() as $column => $default) {
            if ($row->{$column} === null || $row->{$column} === '') {
                $row->{$column} = $default;
            }
        }

        $row->save();

        app(SystemSettingService::class)->flush();
    }
}
