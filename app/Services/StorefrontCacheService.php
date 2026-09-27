<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Cache for storefront data that is the same for every visitor and only changes when an
 * admin edits the catalogue: header / mobile menu navigation and the home page blocks.
 * (Per-visitor data - cart, wishlist - is never cached here.)
 *
 * Invalidation: every entry is keyed with a version number; flush() bumps the version so
 * all current entries are retired at once. AppServiceProvider calls flush() whenever a
 * product, category, sub-category, brand or product attribute is saved or deleted, and
 * TTL_SECONDS is a safety net for any write that bypasses model events.
 * Works with any cache store (file locally, Redis in production).
 */
class StorefrontCacheService
{
    public const TTL_SECONDS = 600;

    private const VERSION_KEY = 'storefront:version';

    public function remember(string $name, Closure $callback)
    {
        return Cache::remember($this->key($name), self::TTL_SECONDS, $callback);
    }

    public function flush(): void
    {
        if (!Cache::has(self::VERSION_KEY)) {
            Cache::forever(self::VERSION_KEY, 1);
        }
        Cache::increment(self::VERSION_KEY);
    }

    private function key(string $name): string
    {
        return 'storefront:v' . Cache::rememberForever(self::VERSION_KEY, fn () => 1) . ':' . $name;
    }
}
