<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;

class TwilioWebhookValidator
{
    public function validate(Request $request): bool
    {
        $authToken = config('twilio.auth_token');

        if (! filled($authToken)) {
            return false;
        }

        $signature = $request->header('X-Twilio-Signature', '');

        if ($signature === '') {
            return false;
        }

        $url = $this->resolveRequestUrl($request);
        $params = $request->post();

        ksort($params);

        $data = $url;

        foreach ($params as $key => $value) {
            $data .= $key . $value;
        }

        $expected = base64_encode(hash_hmac('sha1', $data, $authToken, true));

        return hash_equals($expected, $signature);
    }

    private function resolveRequestUrl(Request $request): string
    {
        $configuredUrl = config('twilio.webhook_base_url');

        if (filled($configuredUrl)) {
            return rtrim($configuredUrl, '/') . '/' . ltrim($request->path(), '/');
        }

        return $request->fullUrl();
    }
}
