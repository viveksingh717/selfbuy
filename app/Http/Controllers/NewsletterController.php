<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use App\Services\OfferCouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class NewsletterController extends Controller
{
    /** Popup state cookie: dismissed | never | subscribed (also read by the popup JS). */
    public const POPUP_COOKIE = 'sb_newsletter_popup';

    /**
     * Newsletter popup sign-up. The popup posts via AJAX: validation failures come back
     * as the standard 422 JSON, everything else as { success, message, data.status }.
     * A plain form post (JS failed to load) is redirected back with a flash message instead.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email'   => ['required', 'string', 'email:rfc,filter', 'max:190'],
            // Honeypot - real users never fill this hidden field.
            'website' => ['nullable', 'size:0'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
            'website.size'   => 'Spam detected.',
        ]);

        $email      = Str::lower(trim($validated['email']));
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber && $subscriber->isSubscribed()) {
            return $this->respond($request, 'You are already subscribed to our newsletter.', 'already_subscribed');
        }

        $attributes = [
            'status'          => 'subscribed',
            'source'          => 'popup',
            'ip_address'      => $request->ip(),
            'user_agent'      => Str::limit((string) $request->userAgent(), 250, ''),
            'subscribed_at'   => now(),
            'unsubscribed_at' => null,
            'user_id'         => Auth::guard('web')->id() ?? $subscriber?->user_id,
        ];

        if ($subscriber) {
            // Previously unsubscribed - welcome them back.
            $subscriber->update($attributes);
            $status = 'resubscribed';
        } else {
            $subscriber = NewsletterSubscriber::create(['email' => $email] + $attributes);
            $status = 'subscribed';
        }

        $this->issueCoupon($subscriber);

        $this->sendWelcomeMail($subscriber);

        return $this->respond($request, 'Thank you for subscribing! Watch your inbox for our latest offers.', $status);
    }

    private function respond(Request $request, string $message, string $status)
    {
        $response = $request->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => $message,
                'data'    => ['status' => $status],
            ])
            : back()->with('newsletter_message', $message);

        // Stop the popup for this browser (1 year). Not httpOnly: the popup JS reads it too.
        return $response->withCookie(cookie(
            self::POPUP_COOKIE, 'subscribed', 60 * 24 * 365, '/', null, $request->isSecure(), false, false, 'Lax'
        ));
    }

    /**
     * Should the newsletter popup be rendered for this request? Not for visitors who
     * subscribed / opted out / closed it recently (cookie), nor for a logged-in customer
     * who is already subscribed - so nothing (not even a stale cached script) can open it.
     */
    public static function shouldShowPopup(Request $request): bool
    {
        if (!home_setting('newsletter_popup_enabled') || $request->routeIs('cart.*', 'checkout.*', 'payment.*')) {
            return false;
        }

        if ($request->cookie(self::POPUP_COOKIE)) {
            return false;
        }

        $user = Auth::guard('web')->user();
        if ($user) {
            return !NewsletterSubscriber::subscribed()
                ->where(fn ($q) => $q->where('user_id', $user->id)->orWhere('email', Str::lower((string) $user->email)))
                ->exists();
        }

        return true;
    }

    /** Signed link from the welcome email (and the List-Unsubscribe header). */
    public function unsubscribe(NewsletterSubscriber $subscriber)
    {
        if ($subscriber->isSubscribed()) {
            $subscriber->update([
                'status'          => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);
        }

        return view('partials.newsletter_unsubscribed', ['subscriber' => $subscriber]);
    }

    /** First sign-up gets a single-use coupon; a coupon failure must not fail the sign-up. */
    private function issueCoupon(NewsletterSubscriber $subscriber)
    {
        try {
            return app(OfferCouponService::class)->issueForSubscriber($subscriber);
        } catch (Throwable $e) {
            Log::error('Newsletter coupon issue failed: ' . $e->getMessage(), [
                'subscriber_id' => $subscriber->id,
            ]);
            return null;
        }
    }

    /** Best-effort mail - a mail failure must not fail the sign-up. */
    private function sendWelcomeMail(NewsletterSubscriber $subscriber): void
    {
        try {
            Mail::to($subscriber->email)->send(new NewsletterWelcomeMail($subscriber));
        } catch (Throwable $e) {
            Log::error('Newsletter welcome mail failed: ' . $e->getMessage(), [
                'subscriber_id' => $subscriber->id,
            ]);
        }
    }
}
