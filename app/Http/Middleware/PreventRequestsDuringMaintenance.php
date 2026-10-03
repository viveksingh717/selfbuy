<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * Paths that keep working while the store is in maintenance mode.
 *
 * Kept in this class (not in bootstrap/app.php) on purpose: `php artisan down`
 * reads this list into the "down" file, so the pre-rendered maintenance page
 * (served before Laravel boots) lets these paths through too - whether
 * maintenance was switched on from the admin panel, the terminal or CI.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    protected $except = [
        'admin',
        'admin/*',      // the admin panel - otherwise maintenance could not be switched off again
        'webhooks/*',   // payment gateways confirming payments server-to-server
        'payment/*',    // customers already in the middle of paying can finish
    ];
}
