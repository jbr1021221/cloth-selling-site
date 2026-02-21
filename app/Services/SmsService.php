<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Alpha SMS REST API Service
 *
 * Documentation: https://alphasms.com.bd/api
 *
 * Config keys used:
 *   services.alpha_sms.api_key   → ALPHA_SMS_API_KEY in .env
 *   services.alpha_sms.sender_id → ALPHA_SMS_SENDER_ID in .env (optional, defaults to 'ClothStore')
 *   services.alpha_sms.base_url  → override for testing (optional)
 */
class SmsService
{
    protected string $apiKey;
    protected string $senderId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey   = \App\Models\Setting::get('alpha_sms_api_key', config('services.alpha_sms.api_key', ''));
        $this->senderId = \App\Models\Setting::get('sms_sender_id', config('services.alpha_sms.sender_id', 'ClothStore'));
        $this->baseUrl  = config('services.alpha_sms.base_url', 'https://alphasms.com.bd/api/v1/send');
    }

    /**
     * Send an SMS message.
     *
     * @param  string  $phone   Recipient phone number (01XXXXXXXXX or 8801XXXXXXXXX)
     * @param  string  $message Plain-text message body
     * @return bool             True if API returned success, false otherwise
     */
    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('[SmsService] ALPHA_SMS_API_KEY is not set. SMS not sent.', [
                'phone'   => $phone,
                'message' => $message,
            ]);
            return false;
        }

        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::timeout(10)->get($this->baseUrl, [
                'api_key'   => $this->apiKey,
                'type'      => 'text',
                'contacts'  => $phone,
                'senderid'  => $this->senderId,
                'msg'       => $message,
            ]);

            $body = $response->json() ?? $response->body();

            if ($response->successful()) {
                Log::info('[SmsService] SMS sent successfully.', [
                    'phone'    => $phone,
                    'response' => $body,
                ]);
                return true;
            }

            Log::error('[SmsService] SMS API returned an error.', [
                'phone'    => $phone,
                'status'   => $response->status(),
                'response' => $body,
            ]);
            return false;

        } catch (\Throwable $e) {
            // Never let an SMS failure break the main request
            Log::error('[SmsService] SMS exception: ' . $e->getMessage(), [
                'phone' => $phone,
            ]);
            return false;
        }
    }

    /**
     * Normalise a Bangladeshi phone number to 8801XXXXXXXXX format.
     * Handles: 01712345678  →  8801712345678
     *          +8801712345678 → 8801712345678
     *          8801712345678  → (unchanged)
     */
    protected function normalizePhone(string $phone): string
    {
        // Strip spaces, dashes, parentheses
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '88' . $phone;          // 01X... → 8801X...
        } elseif (str_starts_with($phone, '1') && strlen($phone) === 10) {
            $phone = '880' . $phone;          // 1X... (10 digits) → 8801X...
        }

        return $phone;
    }
}
