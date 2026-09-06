<?php

use App\Services\SystemSettingService;

function common_function()
{
    return "Hello helper";
}

if (!function_exists('setting')) {
    /**
     * Read a site-wide system setting.
     *
     *   setting('site_name')
     *   setting('contact_phone', 'N/A')
     */
    function setting(string $key, $default = null)
    {
        return app(SystemSettingService::class)->get($key, $default);
    }
}

if (!function_exists('setting_asset')) {
    /**
     * Full URL for a file/image setting (logo, favicon, payment image ...).
     * Returns $default when nothing is uploaded.
     *
     *   setting_asset('logo_horizontal', asset('horizontal_logo.png'))
     */
    function setting_asset(string $key, $default = null)
    {
        return app(SystemSettingService::class)->assetUrl($key) ?? $default;
    }
}

if (!function_exists('settings_all')) {
    /** The whole [key => value] map (cached). */
    function settings_all(): array
    {
        return app(SystemSettingService::class)->all();
    }
}
