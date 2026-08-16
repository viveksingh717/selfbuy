<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The session ID is regenerated as part of the login process, so the guest
        // session ID must be captured on Attempting (fired before login) rather
        // than read again inside the Login listener (fired after regeneration).
        $preAuthSessionId = null;

        Event::listen(function (Attempting $event) use (&$preAuthSessionId) {
            if ($event->guard === 'web') {
                $preAuthSessionId = Session::getId();
            }
        });

        Event::listen(function (Login $event) use (&$preAuthSessionId) {
            if ($event->guard === 'web') {
                app(CartService::class)->mergeGuestCartIntoUser($preAuthSessionId ?? Session::getId(), $event->user->id);
            }
        });

        View::composer('layouts.inc.header', function ($view) {
            $cartService = app(CartService::class);
            $cartItems = $cartService->getCartItems();

            $view->with([
                'headerCartItems' => $cartItems->take(5),
                'headerCartCount' => (int) $cartItems->sum('qty'),
                'headerCartTotal' => (float) $cartItems->sum(fn ($item) => $item->line_total),
            ]);
        });
    }
}
