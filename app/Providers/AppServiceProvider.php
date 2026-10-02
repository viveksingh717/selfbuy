<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use App\Models\SubCategory;
use App\Models\ContactUs;
use App\Models\Order;
use App\Services\CartService;
use App\Services\StorefrontCacheService;
use App\Services\WishlistService;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope is a dev-only package (require-dev), so it isn't installed on a
        // `composer install --no-dev` production server - only register it locally.
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureEnvironment();
        $this->configureRateLimiting();

        // Storefront cache (menus + home blocks): drop it whenever the catalogue changes in admin.
        $flushStorefront = fn () => app(StorefrontCacheService::class)->flush();
        foreach ([ProductModel::class, Category::class, SubCategory::class, Brand::class, ProductAttribute::class] as $model) {
            $model::saved($flushStorefront);
            $model::deleted($flushStorefront);
        }

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

        // "Meet Our Team" cards on the storefront About Us page.
        View::composer('partials.about_us', function ($view) {
            $view->with('teamMembers', \App\Models\TeamMember::active()->ordered()->get());
        });

        // ── Admin header: notification + contact-message badges/dropdowns ──
        View::composer('admin.layouts.inc.header', function ($view) {
            if (!Auth::guard('admin')->check()) {
                return;
            }

            $view->with([
                'headerNotifications'  => AdminNotification::latestFirst()->limit(8)->get(),
                'headerNotifUnread'    => AdminNotification::unread()->count(),
                'headerContactUnread'  => ContactUs::where('status', 'unread')->count(),
                'headerContactRecent'  => ContactUs::where('status', 'unread')->latest()->limit(6)->get(),
            ]);
        });

        // A new storefront order raises an admin notification (no controller
        // changes needed - the model event covers every code path that creates one).
        Order::created(function (Order $order) {
            AdminNotification::record([
                'type'  => 'order',
                'title' => 'New order ' . $order->order_number,
                'body'  => trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '')) . ' · '
                    . setting('currency_symbol', '₹') . number_format((float) $order->total, 2),
                'url'   => route('admin.orders.show', $order->id),
                'icon'  => 'fa-shopping-cart',
                'data'  => ['order_id' => $order->id, 'order_number' => $order->order_number],
            ]);
        });
    }

    /**
     * Per-environment behaviour (APP_ENV = local / staging / production).
     * Anything that should differ between your machine and the live site goes here.
     */
    private function configureEnvironment(): void
    {
        // Generate https:// links even when the server sits behind a proxy / load
        // balancer that talks plain HTTP to PHP (payment return URLs, emails, invoices).
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        // Block migrate:fresh / migrate:refresh / migrate:reset / db:wipe on the live database.
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    /**
     * Named rate limiters, applied per route with ->middleware('throttle:<name>') in routes/web.php.
     * Auth endpoints are keyed by IP *and* by the email being tried, so one attacker can't
     * hammer many accounts and many IPs can't hammer one account.
     */
    private function configureRateLimiting(): void
    {
        $ip    = fn (Request $r) => $r->ip();
        $email = fn (Request $r) => Str::lower(trim((string) $r->input('email')));
        $owner = fn (Request $r) => Auth::guard('web')->id() ?: $r->ip(); // logged-in customer, else IP

        // Customer + admin password logins.
        RateLimiter::for('login', fn (Request $r) => [
            Limit::perMinute(5)->by('login:'.$email($r).'|'.$ip($r))->response($this->tooManyAttempts('login attempts')),
            Limit::perMinute(20)->by('login-ip:'.$ip($r))->response($this->tooManyAttempts('login attempts')),
        ]);
        RateLimiter::for('admin-login', fn (Request $r) => [
            Limit::perMinute(5)->by('admin-login:'.$email($r).'|'.$ip($r))->response($this->tooManyAttempts('login attempts')),
            Limit::perMinute(20)->by('admin-login-ip:'.$ip($r))->response($this->tooManyAttempts('login attempts')),
        ]);

        // OTP: each code already allows 5 wrong tries (OtpService); this caps guessing across codes.
        RateLimiter::for('otp-verify', fn (Request $r) => Limit::perMinute(10)->by('otp-verify:'.$ip($r))
            ->response($this->tooManyAttempts('verification attempts')));
        // Resend sends an email + SMS - cap it to stop the site being used to spam someone.
        RateLimiter::for('otp-resend', fn (Request $r) => [
            Limit::perMinute(3)->by('otp-resend:'.$ip($r))->response($this->tooManyAttempts('code requests')),
            Limit::perHour(10)->by('otp-resend-hour:'.$ip($r))->response($this->tooManyAttempts('code requests')),
        ]);

        RateLimiter::for('register', fn (Request $r) => [
            Limit::perMinute(5)->by('register:'.$ip($r))->response($this->tooManyAttempts('sign-up attempts')),
            Limit::perHour(20)->by('register-hour:'.$ip($r))->response($this->tooManyAttempts('sign-up attempts')),
        ]);

        // Forgot / reset password (customer + admin) - each request sends an email.
        RateLimiter::for('password-reset', fn (Request $r) => [
            Limit::perMinute(3)->by('pw-reset:'.$email($r).'|'.$ip($r))->response($this->tooManyAttempts('password reset requests')),
            Limit::perHour(10)->by('pw-reset-hour:'.$ip($r))->response($this->tooManyAttempts('password reset requests')),
        ]);

        // Stops guessing coupon codes.
        RateLimiter::for('coupon', fn (Request $r) => Limit::perMinute(10)->by('coupon:'.$owner($r))
            ->response($this->tooManyAttempts('coupon attempts')));

        RateLimiter::for('checkout', fn (Request $r) => Limit::perMinute(10)->by('checkout:'.$owner($r))
            ->response($this->tooManyAttempts('order attempts')));

        // Cart / wishlist / review votes - generous, just stops scripted abuse.
        RateLimiter::for('cart', fn (Request $r) => Limit::perMinute(60)->by('cart:'.$owner($r))
            ->response($this->tooManyAttempts('requests')));

        RateLimiter::for('reviews', fn (Request $r) => Limit::perMinute(10)->by('reviews:'.$owner($r))
            ->response($this->tooManyAttempts('review submissions')));
    }

    /**
     * 429 response in the shape the storefront JS already reads (res.message / res.success),
     * or - for plain form posts (admin login etc.) - back to the form with the error flashed.
     */
    private function tooManyAttempts(string $what): \Closure
    {
        return function (Request $request, array $headers) use ($what) {
            $seconds = (int) ($headers['Retry-After'] ?? 60);
            $message = "Too many {$what}. Please try again in {$seconds} seconds.";

            if ($request->expectsJson()) {
                return response()->json([
                    'status'     => 429,
                    'type'       => 'error',
                    'success'    => false,
                    'message'    => $message,
                    'validation' => [],
                    'data'       => [],
                ], 429, $headers);
            }

            return back()->with('error', $message)->withInput($request->except(['password', 'password_confirmation']))->withHeaders($headers);
        };
    }
}
