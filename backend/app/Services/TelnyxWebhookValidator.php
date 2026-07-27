<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelnyxWebhookValidator
{
    public function validate(Request $request): bool
    {
        $publicKey = config('telnyx.webhook_public_key');

        // Allow unsigned webhooks when no public key is configured (local/dev).
        if (! filled($publicKey)) {
            return true;
        }

        $signature = (string) $request->header('telnyx-signature-ed25519', '');
        $timestamp = (string) $request->header('telnyx-timestamp', '');
        $payload = $request->getContent();

        if ($signature === '' || $timestamp === '') {
            return false;
        }

        if (! function_exists('sodium_crypto_sign_verify_detached')) {
            Log::warning('Telnyx webhook validation skipped: sodium extension unavailable');

            return false;
        }

        try {
            $decodedKey = $this->decodePublicKey((string) $publicKey);
            $decodedSignature = base64_decode($signature, true);

            if ($decodedKey === null || $decodedSignature === false) {
                return false;
            }

            $signedPayload = $timestamp . '|' . $payload;

            return sodium_crypto_sign_verify_detached($decodedSignature, $signedPayload, $decodedKey);
        } catch (\Throwable $exception) {
            Log::warning('Telnyx webhook signature validation failed', [
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function decodePublicKey(string $publicKey): ?string
    {
        $raw = base64_decode($publicKey, true);

        if ($raw !== false && strlen($raw) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            return $raw;
        }

        // Support hex-encoded public keys from the Telnyx portal.
        if (ctype_xdigit($publicKey) && strlen($publicKey) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES * 2) {
            $hex = hex2bin($publicKey);

            return $hex === false ? null : $hex;
        }

        return null;
    }
}
