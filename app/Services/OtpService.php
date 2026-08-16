<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    private const EXPIRY_MINUTES = 10;
    private const RESEND_COOLDOWN_SECONDS = 30;
    private const MAX_ATTEMPTS = 5;

    public function __construct(private SmsService $smsService)
    {
    }

    /**
     * Two near-simultaneous requests (e.g. a double-click on submit) could otherwise
     * both decide "no recent code exists", each mint and email a different code, and
     * only the last write survives — leaving whichever email the user reads first
     * pointing at an overwritten code. The lock serializes generation per user+purpose
     * so the second request correctly sees the first request's code instead of racing it.
     */
    public function generate(User $user, string $purpose): array
    {
        try {
            return Cache::lock($this->lockKey($user, $purpose), 10)->block(5, function () use ($user, $purpose) {
                $recent = Otp::where('user_id', $user->id)
                    ->where('purpose', $purpose)
                    ->whereNull('consumed_at')
                    ->where('expires_at', '>', now())
                    ->latest()
                    ->first();

                if ($recent && $recent->created_at->gt(now()->subSeconds(self::RESEND_COOLDOWN_SECONDS))) {
                    return ['success' => true, 'message' => 'A verification code was already sent to your email and phone.'];
                }

                return $this->issueNewCode($user, $purpose);
            });
        } catch (LockTimeoutException) {
            Log::warning('OTP: generate lock timed out', ['user_id' => $user->id, 'purpose' => $purpose]);

            return ['success' => false, 'message' => 'Please try again in a moment.'];
        }
    }

    public function resend(User $user, string $purpose): array
    {
        try {
            return Cache::lock($this->lockKey($user, $purpose), 10)->block(5, function () use ($user, $purpose) {
                $last = Otp::where('user_id', $user->id)->where('purpose', $purpose)->latest()->first();

                if ($last && $last->created_at->gt(now()->subSeconds(self::RESEND_COOLDOWN_SECONDS))) {
                    $wait = self::RESEND_COOLDOWN_SECONDS - (int) now()->diffInSeconds($last->created_at);

                    Log::info('OTP: resend blocked by cooldown', ['user_id' => $user->id, 'purpose' => $purpose, 'wait_seconds' => $wait]);

                    return ['success' => false, 'message' => "Please wait {$wait}s before requesting another code."];
                }

                Log::info('OTP: resend requested', ['user_id' => $user->id, 'purpose' => $purpose]);

                // Force a fresh code even if the previous one is still within its validity window.
                return $this->issueNewCode($user, $purpose);
            });
        } catch (LockTimeoutException) {
            Log::warning('OTP: resend lock timed out', ['user_id' => $user->id, 'purpose' => $purpose]);

            return ['success' => false, 'message' => 'Please try again in a moment.'];
        }
    }

    private function issueNewCode(User $user, string $purpose): array
    {
        $otp = (string) random_int(100000, 999999);

        Otp::where('user_id', $user->id)->where('purpose', $purpose)->whereNull('consumed_at')->delete();

        Otp::create([
            'user_id' => $user->id,
            'purpose' => $purpose,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        Log::info('OTP: code generated', ['user_id' => $user->id, 'purpose' => $purpose, 'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES)->toDateTimeString()]);

        $this->dispatch($user, $otp);

        return ['success' => true, 'message' => 'A verification code has been sent to your email and phone.'];
    }

    private function lockKey(User $user, string $purpose): string
    {
        return "otp-generate:{$user->id}:{$purpose}";
    }

    public function verify(User $user, string $purpose, string $inputOtp): array
    {
        $otp = Otp::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (! $otp) {
            Log::warning('OTP: verify failed, no active code', ['user_id' => $user->id, 'purpose' => $purpose]);

            return ['success' => false, 'message' => 'No active code found. Please request a new one.'];
        }

        if ($otp->expires_at->isPast()) {
            Log::warning('OTP: verify failed, code expired or locked out', ['user_id' => $user->id, 'purpose' => $purpose, 'otp_id' => $otp->id]);

            return ['success' => false, 'message' => 'This code has expired or is no longer valid. Please request a new one.'];
        }

        if (! Hash::check($inputOtp, $otp->otp_hash)) {
            $otp->increment('attempts');

            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->update(['expires_at' => now()]);

                Log::warning('OTP: verify failed, max attempts reached', ['user_id' => $user->id, 'purpose' => $purpose, 'otp_id' => $otp->id]);

                return ['success' => false, 'message' => 'Too many incorrect attempts. Please request a new code.'];
            }

            Log::info('OTP: verify failed, incorrect code', ['user_id' => $user->id, 'purpose' => $purpose, 'otp_id' => $otp->id, 'attempts' => $otp->attempts]);

            return ['success' => false, 'message' => 'Invalid code. Please try again.'];
        }

        $otp->update(['consumed_at' => now()]);

        Log::info('OTP: verified successfully', ['user_id' => $user->id, 'purpose' => $purpose, 'otp_id' => $otp->id]);

        return ['success' => true, 'message' => 'Verified.'];
    }

    private function dispatch(User $user, string $otp): void
    {
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            Log::info('OTP: email dispatched', ['user_id' => $user->id, 'to' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('OTP: email send failed: '.$e->getMessage(), ['user_id' => $user->id, 'to' => $user->email]);
        }

        if ($user->phone_number) {
            $smsSent = $this->smsService->sendOtp($user->phone_number, $otp);

            Log::info('OTP: SMS dispatch '.($smsSent ? 'succeeded' : 'failed'), ['user_id' => $user->id, 'phone' => $user->phone_number]);
        } else {
            Log::info('OTP: SMS skipped, no phone number on file', ['user_id' => $user->id]);
        }
    }
}
