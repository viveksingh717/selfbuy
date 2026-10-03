<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Store maintenance mode switch (Admin > System Settings).
 *
 * Uses Laravel's own maintenance mode (`artisan down` / `up`), called in-process,
 * so it works on shared hosting where proc_open is disabled. The admin panel,
 * payment webhooks and payment pages stay reachable - see
 * App\Http\Middleware\PreventRequestsDuringMaintenance.
 */
class MaintenanceController extends Controller
{
    public function enable()
    {
        if (app()->isDownForMaintenance()) {
            return back()->with('alert', 'The store is already in maintenance mode.');
        }

        try {
            Artisan::call('down', [
                '--render' => 'errors::503',   // page served before Laravel boots
                '--retry'  => 60,              // tells browsers / search engines it's temporary
                '--secret' => Str::random(32), // private link for previewing the store
            ]);
        } catch (Throwable $e) {
            Log::error('Maintenance mode: could not enable - '.$e->getMessage());

            return back()->with('error', 'Could not switch on maintenance mode. Please try again.');
        }

        Log::info('Maintenance mode: enabled from admin', ['admin_id' => Auth::guard('admin')->id()]);

        return back()->with('success', 'Maintenance mode is ON. Visitors now see the maintenance page.');
    }

    public function disable()
    {
        if (! app()->isDownForMaintenance()) {
            return back()->with('alert', 'The store is already live.');
        }

        try {
            Artisan::call('up');
        } catch (Throwable $e) {
            Log::error('Maintenance mode: could not disable - '.$e->getMessage());

            return back()->with('error', 'Could not switch off maintenance mode. Please try again.');
        }

        Log::info('Maintenance mode: disabled from admin', ['admin_id' => Auth::guard('admin')->id()]);

        return back()->with('success', 'Maintenance mode is OFF. The store is live again.');
    }

    /** Status + private preview link for the admin view. */
    public static function status(): array
    {
        if (! app()->isDownForMaintenance()) {
            return ['down' => false, 'preview_url' => null];
        }

        $secret = app()->maintenanceMode()->data()['secret'] ?? null;

        return ['down' => true, 'preview_url' => $secret ? url($secret) : null];
    }
}
