<?php

namespace App\Services\WaPilot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaPilotClient
{
    public function isEnabled(): bool
    {
        return (string) get_settings('wapilot_enabled') === '1';
    }

    public function send(string $phone, string $message): array
    {
        $baseUrl = rtrim((string) get_settings('wapilot_api_url'), '/');
        $path = (string) (get_settings('wapilot_send_path') ?: '/api/send');
        $apiKey = (string) get_settings('wapilot_api_key');
        $sender = (string) get_settings('wapilot_sender');

        if ($baseUrl === '' || $apiKey === '') {
            return [
                'success' => false,
                'response' => 'WaPilot API URL or API key is missing.',
            ];
        }

        $url = str_starts_with($path, 'http') ? $path : $baseUrl . '/' . ltrim($path, '/');

        $payload = array_filter([
            'phone' => $phone,
            'number' => $phone,
            'to' => $phone,
            'message' => $message,
            'text' => $message,
            'type' => 'text',
            'sender' => $sender ?: null,
        ], static fn ($value) => $value !== null && $value !== '');

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'api-key' => $apiKey,
                    'X-API-KEY' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $body = $response->body();
            $success = $response->successful();

            if (!$success) {
                Log::warning('WaPilot send failed', [
                    'status' => $response->status(),
                    'body' => $body,
                    'phone' => $phone,
                ]);
            }

            return [
                'success' => $success,
                'response' => $body,
                'status_code' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('WaPilot send exception: ' . $e->getMessage());

            return [
                'success' => false,
                'response' => $e->getMessage(),
            ];
        }
    }

    public function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (!$digits) {
            return null;
        }

        $country = preg_replace('/\D+/', '', (string) (get_settings('wapilot_default_country_code') ?: '20'));

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && $country !== '') {
            $digits = $country . substr($digits, 1);
        }

        return $digits;
    }
}
