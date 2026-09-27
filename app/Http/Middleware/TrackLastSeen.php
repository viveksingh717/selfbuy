<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records when a signed-in storefront customer was last active (users.last_seen_at),
 * for the admin dashboard's "Active now". Writes at most once a minute per user and
 * doesn't touch updated_at.
 */
class TrackLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = Auth::guard('web')->user();
        if ($user && (!$user->last_seen_at || now()->diffInSeconds($user->last_seen_at, true) >= 60)) {
            DB::table('users')->where('id', $user->id)->update(['last_seen_at' => now()]);
        }

        return $response;
    }
}
