<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Middleware\AdminAuthenticate;
use App\Support\DatabaseUnavailable;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminRedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrackLastSeen;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SecurityHeaders::class,
            TrackLastSeen::class,
        ]);

        // Same maintenance check, plus the paths that stay open (admin, payments).
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        );

        $middleware->alias([
            'adminAuth'=>AdminAuthenticate::class,
            'adminGuest'=>AdminRedirectIfAuthenticated::class,
        ]);

        // Payment gateway webhooks are called server-to-server and can't supply a
        // CSRF token; they're authenticated instead via the gateway's own signed
        // request body (verified per-gateway in the controller).
        // Newsletter popup state (dismissed / never / subscribed) is set by the popup JS and read
        // by both JS and Blade, so it must stay a plain, unencrypted cookie. Not sensitive.
        $middleware->encryptCookies(except: [
            'sb_newsletter_popup',
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/razorpay',
            'webhooks/stripe',
            'webhooks/paypal',
            'webhooks/instamojo',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Database down / unreachable / timed out -> friendly 503 page that retries,
        // instead of a generic 500. The error is still reported (log + Bugsnag) as usual.
        // With APP_DEBUG=true (local) the normal debug page is kept for the stack trace.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (config('app.debug') || ! DatabaseUnavailable::matches($e)) {
                return null; // let Laravel handle it normally
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Service temporarily unavailable. Please try again shortly.'], 503)
                    ->header('Retry-After', '30');
            }

            return response()->view('errors.503', ['reason' => 'database'], 503)
                ->header('Retry-After', '30');
        });
    })->create();
