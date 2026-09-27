<?php

use App\Services\HomeSettingService;
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

if (!function_exists('home_setting')) {
    /**
     * Read a storefront home page setting (carousel, headings, CTA ...).
     *
     *   home_setting('trendy_products_title')
     */
    function home_setting(string $key, $default = null)
    {
        return app(HomeSettingService::class)->get($key, $default);
    }
}

if (!function_exists('home_asset')) {
    /**
     * Full URL for a home page image - the uploaded file, else the bundled
     * placeholder from the schema.
     *
     *   home_asset('slide_1_image')
     */
    function home_asset(string $key, $default = null)
    {
        return app(HomeSettingService::class)->assetUrl($key) ?? $default;
    }
}

if (!function_exists('home_link')) {
    /**
     * Turn an admin-entered link into an href: absolute URLs, anchors and
     * mailto/tel links pass through, site paths like "/shop" become url('/shop').
     */
    function home_link(?string $link, string $fallback = '#'): string
    {
        $link = trim((string) $link);
        if ($link === '') {
            return $fallback;
        }
        if (preg_match('#^(https?:)?//|^(\#|mailto:|tel:)#i', $link)) {
            return $link;
        }
        return url($link);
    }
}

if (!function_exists('home_slides')) {
    /** Active home page carousel slides, in display order. */
    function home_slides()
    {
        return app(HomeSettingService::class)->slides();
    }
}
