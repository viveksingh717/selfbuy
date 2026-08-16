<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\WishlistService;
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

        // Note: in the current flow, Auth::guard('web')->login() is only ever
        // called directly from OtpController::verify() (after 2FA succeeds),
        // never via Auth::attempt() — so Attempting/Login never actually fire
        // for a real login, and the merge calls that matter live in
        // OtpController::verify() itself, which captures the pre-login session
        // id correctly. This listener is kept only in case something still
        // authenticates via attempt() in the future.
        Event::listen(function (Login $event) use (&$preAuthSessionId) {
            if ($event->guard === 'web') {
                $sessionId = $preAuthSessionId ?? Session::getId();

                app(CartService::class)->mergeGuestCartIntoUser($sessionId, $event->user->id);
                app(WishlistService::class)->mergeGuestWishlistIntoUser($sessionId, $event->user->id);
            }
        });

        View::composer('layouts.inc.header', function ($view) {
            $cartService = app(CartService::class);
            $cartItems = $cartService->getCartItems();

            $view->with([
                'headerCartItems' => $cartItems->take(5),
                'headerCartCount' => (int) $cartItems->sum('qty'),
                'headerCartTotal' => (float) $cartItems->sum(fn ($item) => $item->line_total),
                'headerWishlistCount' => app(WishlistService::class)->getWishlistCount(),
            ]);
        });

        // Lets product listing/detail views show a filled heart for items already
        // wishlisted, without every controller that renders them needing to remember
        // to fetch and pass this down — same rationale as the header composer above.
        View::composer(['shop.partials.product_grid', 'shop.product_details'], function ($view) {
            $view->with('wishlistedProductIds', app(WishlistService::class)->getWishlistedProductIds());
        });
    }
}
