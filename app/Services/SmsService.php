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

    /**
     * Generic transactional SMS (order confirmed/failed, etc.) — a distinct
     * method from sendOtp() because MSG91's OTP API and its general SMS/Flow
     * API are different endpoints with different template requirements.
     */
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        return match (config('services.sms.driver', 'log')) {
            'msg91' => $this->sendMessageViaMsg91($phoneNumber, $message),
            default => $this->sendMessageViaLog($phoneNumber, $message),
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

    private function sendMessageViaLog(string $phoneNumber, string $message): bool
    {
        Log::info("[SMS log-driver] To: {$phoneNumber} | {$message}");

        return true;
    }

    private function sendMessageViaMsg91(string $phoneNumber, string $message): bool
    {
        $templateId = config('services.msg91.transactional_template_id');

        if (empty($templateId)) {
            // Transactional (non-OTP) SMS in India requires a DLT-registered template —
            // MSG91's Flow API needs that template ID, which is separate from the OTP
            // template. Falling back to the log driver rather than sending a request
            // that MSG91 would reject anyway.
            Log::warning('MSG91 transactional template not configured; falling back to log driver', ['mobile' => $phoneNumber]);

            return $this->sendMessageViaLog($phoneNumber, $message);
        }

        try {
            $response = Http::withHeaders(['authkey' => config('services.msg91.auth_key')])
                ->post('https://control.msg91.com/api/v5/flow/', [
                    'template_id' => $templateId,
                    'sender' => config('services.msg91.sender_id'),
                    'short_url' => '0',
                    'mobiles' => $phoneNumber,
                    'var1' => $message,
                ]);

            if ($response->successful()) {
                Log::info('MSG91 SMS send succeeded', ['mobile' => $phoneNumber]);
            } else {
                Log::error('MSG91 SMS send failed: '.$response->body(), ['mobile' => $phoneNumber]);
            }

            return $response->successful();
        } catch (Throwable $e) {
            Log::error('MSG91 SMS send exception: '.$e->getMessage());

            return false;
        }
    }
}
