<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MaqsamService
{
    public function isConfigured(): bool
    {
        $credentials = $this->getCredentials();

        return $credentials['base_url'] !== ''
            && $credentials['access_key'] !== ''
            && $credentials['access_secret'] !== '';
    }

    /**
     * @return array{base_url: string, access_key: string, access_secret: string}
     */
    public function getCredentials(): array
    {
        return [
            'base_url' => $this->normalizeBaseUrl(
                (string) (AppSetting::where('name', 'maqsam_system_base_url')->value('value') ?? '')
            ),
            'access_key' => trim((string) (AppSetting::where('name', 'maqsam_system_access_key')->value('value') ?? '')),
            'access_secret' => trim((string) (AppSetting::where('name', 'maqsam_system_access_token')->value('value') ?? '')),
        ];
    }

    public function generateAutologinToken(string $userEmail): string
    {
        $this->ensureConfigured();

        $credentials = $this->getCredentials();

        $response = Http::timeout(15)
            ->withBasicAuth($credentials['access_key'], $credentials['access_secret'])
            ->asForm()
            ->post($this->apiUrl('/v2/token'), [
                'UserEmail' => $userEmail,
            ]);

        if (! $response->successful()) {
            $message = $response->json('message') ?? $response->body();
            throw new RuntimeException(is_string($message) ? $message : __('words.caller-maqsam-login-failed'));
        }

        $token = $response->json('result.token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException(__('words.caller-maqsam-login-failed'));
        }

        return $token;
    }

    public function buildAutologinUrl(string $token, string $continuePath = '/phone/dialer'): string
    {
        return $this->portalUrl('/autologin').'?'.http_build_query([
            'auth_token' => $token,
            'continue_path' => $continuePath,
        ]);
    }

    public function buildAutodialUrl(string $phone): string
    {
        return $this->portalUrl('/phone/dialer#autodial='.$this->normalizePhone($phone));
    }

    /**
     * @return array<string, mixed>
     */
    public function createCall(string $agentEmail, string $phone, ?string $caller = null): array
    {
        $this->ensureConfigured();

        $credentials = $this->getCredentials();
        $normalizedPhone = $this->normalizePhone($phone);

        $payload = [
            'email' => $agentEmail,
            'phone' => $normalizedPhone,
        ];

        if ($caller !== null && trim($caller) !== '') {
            $payload['caller'] = $this->normalizePhone($caller);
        }

        $response = Http::timeout(15)
            ->withBasicAuth($credentials['access_key'], $credentials['access_secret'])
            ->asForm()
            ->post($this->apiUrl('/v3/calls'), $payload);

        $body = $response->json();

        if (! $response->successful()) {
            $message = is_array($body) ? ($body['message'] ?? __('words.caller-dial-failed')) : __('words.caller-dial-failed');
            throw new RuntimeException(is_string($message) ? $message : __('words.caller-dial-failed'));
        }

        return is_array($body) ? $body : [];
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            throw new RuntimeException(__('words.caller-invalid-phone'));
        }

        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }

        if (strlen($digits) === 9 && str_starts_with($digits, '5')) {
            $digits = '966'.$digits;
        }

        return $digits;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(__('words.caller-maqsam-not-configured'));
        }
    }

    private function apiUrl(string $path): string
    {
        return 'https://api.'.$this->getCredentials()['base_url'].$path;
    }

    private function portalUrl(string $path = ''): string
    {
        return 'https://portal.'.$this->getCredentials()['base_url'].$path;
    }

    private function normalizeBaseUrl(string $baseUrl): string
    {
        $baseUrl = trim($baseUrl);

        if ($baseUrl === '') {
            return '';
        }

        $baseUrl = preg_replace('#^https?://#', '', $baseUrl) ?? $baseUrl;
        $baseUrl = preg_replace('#^(api|portal)\.#', '', $baseUrl) ?? $baseUrl;
        $baseUrl = explode('/', $baseUrl)[0];

        return rtrim($baseUrl, '/');
    }
}
