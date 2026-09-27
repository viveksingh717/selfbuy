<?php

namespace App\Services;

use App\Models\CouponModel;
use App\Models\NewsletterSubscriber;
use App\Models\User;

/**
 * Issues personal single-use coupons for the storefront offers:
 *  - newsletter sign-up  (rules: Admin > Home Settings > Newsletter Popup)
 *  - first account sign-up / welcome offer (rules: Admin > Home Settings > Sign Up Offer)
 *
 * Single-use is enforced by usage_limit = 1, which CartService::validateCoupon already
 * checks (and used_count is incremented when an order is placed), so checkout needs
 * no changes. Each coupon is linked to its owner so it is only ever issued once.
 */
class OfferCouponService
{
    // No 0/O/1/I so codes are easy to read and type from an email.
    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Newsletter subscriber's coupon - issued on first sign-up only. Re-subscribing returns
     * the existing one, so nobody collects a new code by unsubscribing.
     */
    public function issueForSubscriber(NewsletterSubscriber $subscriber): ?CouponModel
    {
        if ($subscriber->coupon_id) {
            return $subscriber->coupon;
        }

        if (!home_setting('newsletter_coupon_enabled')) {
            return null;
        }

        $coupon = $this->create([
            'percent'      => home_setting('newsletter_popup_offer_percent'),
            'max_discount' => home_setting('newsletter_coupon_max_discount'),
            'min_order'    => home_setting('newsletter_coupon_min_order'),
            'valid_days'   => home_setting('newsletter_coupon_valid_days', 30),
        ], 'NL-', 'Newsletter', "Newsletter sign-up offer for {$subscriber->email}. Single use.");

        if ($coupon) {
            $subscriber->update(['coupon_id' => $coupon->id]);
            $subscriber->setRelation('coupon', $coupon);
        }

        return $coupon;
    }

    /** New customer's welcome coupon - once per account. */
    public function issueWelcomeFor(User $user): ?CouponModel
    {
        if ($user->welcome_coupon_id) {
            return $user->welcomeCoupon;
        }

        if (!home_setting('signup_offer_coupon_enabled')) {
            return null;
        }

        $coupon = $this->create([
            'percent'      => home_setting('signup_offer_percent'),
            'max_discount' => home_setting('signup_offer_max_discount'),
            'min_order'    => home_setting('signup_offer_min_order'),
            'valid_days'   => home_setting('signup_offer_valid_days', 30),
        ], 'WL-', 'Welcome', "Welcome offer for new account {$user->email} (user #{$user->id}). Single use.");

        if ($coupon) {
            $user->forceFill(['welcome_coupon_id' => $coupon->id])->save();
            $user->setRelation('welcomeCoupon', $coupon);
        }

        return $coupon;
    }

    /** Last day the coupon can be used (see the expiry note in create()). */
    public static function lastValidDay(CouponModel $coupon)
    {
        return $coupon->expiry_date?->copy()->subDay();
    }

    /** Still redeemable: active, not expired, not used up. */
    public static function isUsable(?CouponModel $coupon): bool
    {
        return $coupon
            && (int) $coupon->status === 1
            && !($coupon->expiry_date && $coupon->expiry_date->isPast())
            && (is_null($coupon->usage_limit) || $coupon->used_count < $coupon->usage_limit);
    }

    /** "12.50" -> "12.5", "10.00" -> "10". */
    public static function percentText($percent): string
    {
        return rtrim(rtrim(number_format((float) $percent, 2, '.', ''), '0'), '.');
    }

    /**
     * A personal single-use percentage coupon, or null when no valid percentage is set.
     *
     * @param  array  $rules  percent, max_discount (empty = no cap), min_order, valid_days
     */
    private function create(array $rules, string $prefix, string $label, string $description): ?CouponModel
    {
        $percent = $rules['percent'];
        if (!is_numeric($percent) || $percent <= 0 || $percent > 100) {
            return null;
        }

        $days        = max(1, (int) $rules['valid_days']);
        $maxDiscount = $rules['max_discount'];
        $code        = $this->uniqueCode($prefix);

        return CouponModel::create([
            'coupon_name'         => "{$label} " . self::percentText($percent) . "% Off ({$code})", // coupon_name is unique
            'coupon_code'         => $code,
            'description'         => $description,
            'discount_type'       => 'percentage',
            'discount_value'      => (float) $percent,
            'min_order_amount'    => (float) ($rules['min_order'] ?: 0),
            'max_discount_amount' => is_numeric($maxDiscount) && $maxDiscount > 0 ? (float) $maxDiscount : null,
            'start_date'          => today(),
            // validateCoupon() treats expiry_date as expired from 00:00 that day, so the last
            // usable day is the day before - add one so the code works for $days full days.
            'expiry_date'         => today()->addDays($days + 1),
            'usage_limit'         => 1,
            'used_count'          => 0,
            'per_user_limit'      => 1,
            'applicable_to'       => 'all',
            'status'              => 1,
        ]);
    }

    private function uniqueCode(string $prefix): string
    {
        do {
            $code = $prefix;
            for ($i = 0; $i < 8; $i++) {
                $code .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }
        } while (CouponModel::where('coupon_code', $code)->exists());

        return $code;
    }
}
