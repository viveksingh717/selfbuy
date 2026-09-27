<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminAuthenticate;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminRedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;

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
        ]);

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
        //
    })->create();
