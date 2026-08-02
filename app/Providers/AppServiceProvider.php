<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
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
        Event::listen(function (Login $event) {
            if ($event->guard === 'web') {
                app(CartService::class)->mergeGuestCartIntoUser(session()->getId(), $event->user->id);
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
