<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Send Webhook notification to merchant endpoint
     */
    public static function send($merchant, array $payload)
    {
        if (!$merchant || empty($merchant->webhook_url)) {
            return false;
        }

        try {
            Http::timeout(5)
                ->withHeaders([
                    'X-Webhook-Secret' => $merchant->webhook_secret,
                    'Content-Type'     => 'application/json',
                    'User-Agent'       => 'Barakah-Courier-Webhook/1.0',
                ])
                ->post($merchant->webhook_url, $payload);

            return true;
        } catch (\Exception $e) {
            Log::error("Webhook send failed for merchant ID {$merchant->id} - " . $e->getMessage());
            return false;
        }
    }
}
