<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TwilioVerifyService
{
    private Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => 'https://verify.twilio.com/v2/',
            'auth' => [
                config('twilio.account_sid'),
                config('twilio.auth_token'),
            ],
            'http_errors' => false,
        ]);
    }

    public function isConfigured(): bool
    {
        return filled(config('twilio.account_sid'))
            && filled(config('twilio.auth_token'))
            && filled(config('twilio.verify_service_sid'));
    }

    /**
     * Send an OTP via Twilio Verify (POST /v2/Services/{ServiceSid}/Verifications).
     *
     * @see https://www.twilio.com/docs/verify/api/verification
     */
    public function sendSmsCode(string $phone): string
    {
        $to = $this->toE164($phone);

        $response = $this->client->post(
            'Services/' . config('twilio.verify_service_sid') . '/Verifications',
            [
                'form_params' => [
                    'To' => $to,
                    'Channel' => 'sms',
                    'Locale' => config('twilio.verify_locale'),
                ],
            ]
        );

        $payload = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() >= 400) {
            Log::error('Twilio Verify send failed', [
                'status' => $response->getStatusCode(),
                'to' => $to,
                'response' => $payload,
            ]);

            throw new RuntimeException('Failed to send verification code.');
        }

        return $payload['status'] ?? 'pending';
    }

    /**
     * Check a submitted OTP (POST /v2/Services/{ServiceSid}/VerificationCheck).
     *
     * @see https://www.twilio.com/docs/verify/api/verification-check
     */
    public function checkCode(string $phone, string $code): bool
    {
        $response = $this->client->post(
            'Services/' . config('twilio.verify_service_sid') . '/VerificationCheck',
            [
                'form_params' => [
                    'To' => $this->toE164($phone),
                    'Code' => $code,
                ],
            ]
        );

        $payload = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() >= 400) {
            Log::warning('Twilio Verify check failed', [
                'status' => $response->getStatusCode(),
                'response' => $payload,
            ]);

            return false;
        }

        return ($payload['status'] ?? '') === 'approved';
    }

    /**
     * Convert the app's KSA phone format (9665xxxxxxxx) to E.164 (+9665xxxxxxxx).
     */
    public function toE164(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (! str_starts_with($digits, '+')) {
            if (! str_starts_with($digits, '966') && str_starts_with($digits, '05')) {
                $digits = '966' . substr($digits, 1);
            }

            if (! str_starts_with($digits, '966') && str_starts_with($digits, '5')) {
                $digits = '966' . $digits;
            }

            $digits = '+' . $digits;
        }

        return $digits;
    }
}
