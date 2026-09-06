<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'cod_enabled'      => 'boolean',
    ];

    /** The one and only settings row (created on first access). */
    public static function instance(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
