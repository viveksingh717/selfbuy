<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExchangeRateService
{
    /**
     * Only needed for gateways that can't charge in the store's native
     * currency (currently just PayPal on an India-registered account, which
     * PayPal refuses INR for). Cached for an hour either way — including a
     * fallback result — so a live-lookup outage doesn't hammer the API on
     * every checkout attempt, at the cost of the fallback rate sticking
     * around for up to an hour once it's used.
     */
    public function inrToUsdRate(): float
    {
        return Cache::remember('exchange_rate_inr_usd', 3600, function () {
            try {
                $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/INR')->throw();
                $rate = (float) $response->json('rates.USD');

                if ($rate > 0) {
                    return $rate;
                }
            } catch (Throwable $e) {
                Log::warning('ExchangeRate: live INR->USD rate fetch failed, using fallback: '.$e->getMessage());
            }

            return (float) config('services.exchange_rate.inr_usd_fallback', 0.012);
        });
    }

    public function convertInrToUsd(float $amountInr): float
    {
        return round($amountInr * $this->inrToUsdRate(), 2);
    }
}
