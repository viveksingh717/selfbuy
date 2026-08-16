<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsService
{
    public function sendOtp(string $phoneNumber, string $otp): bool
    {
        return match (config('services.sms.driver', 'log')) {
            'msg91' => $this->sendViaMsg91($phoneNumber, $otp),
            default => $this->sendViaLog($phoneNumber, $otp),
        };
    }

    private function sendViaLog(string $phoneNumber, string $otp): bool
    {
        Log::info("[SMS log-driver] OTP {$otp} for {$phoneNumber}");

        return true;
    }

    private function sendViaMsg91(string $phoneNumber, string $otp): bool
    {
        try {
            $response = Http::asForm()->post('https://control.msg91.com/api/v5/otp', [
                'authkey' => config('services.msg91.auth_key'),
                'template_id' => config('services.msg91.template_id'),
                'mobile' => $phoneNumber,
                'otp' => $otp,
                'sender' => config('services.msg91.sender_id'),
            ]);

            if ($response->successful()) {
                Log::info('MSG91 OTP send succeeded', ['mobile' => $phoneNumber]);
            } else {
                Log::error('MSG91 OTP send failed: '.$response->body(), ['mobile' => $phoneNumber]);
            }

            return $response->successful();
        } catch (Throwable $e) {
            Log::error('MSG91 OTP send exception: '.$e->getMessage());

            return false;
        }
    }
}
