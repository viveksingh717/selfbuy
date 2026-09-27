<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline browser security headers for every web response (storefront + admin).
 *
 * No Content-Security-Policy on purpose: the theme relies on inline scripts and several
 * CDNs, so a CSP needs its own careful rollout. `payment` is left out of the
 * Permissions-Policy so embedded payment-gateway checkouts keep working.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Frame-Options', 'SAMEORIGIN');              // no clickjacking via foreign iframes
        $headers->set('X-Content-Type-Options', 'nosniff');          // no MIME-type guessing
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        // Don't advertise the PHP version (PHP sets this itself, outside the Response object).
        $headers->remove('X-Powered-By');
        if (!headers_sent()) {
            header_remove('X-Powered-By');
        }

        // HTTPS only: tell browsers to never use plain HTTP for this site again (1 year).
        if ($request->isSecure() && app()->isProduction()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
