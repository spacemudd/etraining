<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class ChatWebPushService
{
    public function isConfigured(): bool
    {
        $public = (string) config('chat_pwa.vapid.public_key');
        $private = (string) config('chat_pwa.vapid.private_key');

        return $public !== '' && $private !== '';
    }

    public function publicKey(): ?string
    {
        $key = (string) config('chat_pwa.vapid.public_key');

        return $key !== '' ? $key : null;
    }

    /**
     * @param  array{title: string, body: string, url: string}  $payload
     */
    public function sendToSubscription(PushSubscription $subscription, array $payload): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        try {
            $webPush = $this->makeWebPush();
            $webPushSubscription = Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => $subscription->content_encoding ?: 'aesgcm',
            ]);

            $report = $webPush->sendOneNotification(
                $webPushSubscription,
                json_encode($payload, JSON_UNESCAPED_UNICODE)
            );

            if ($report->isSuccess()) {
                return true;
            }

            $response = $report->getResponse();
            $statusCode = $response ? $response->getStatusCode() : null;

            if (in_array($statusCode, [404, 410], true)) {
                $subscription->delete();

                return false;
            }

            Log::warning('Chat web push failed', [
                'subscription_id' => $subscription->id,
                'status' => $statusCode,
                'reason' => $report->getReason(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Chat web push exception', [
                'subscription_id' => $subscription->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    protected function makeWebPush(): WebPush
    {
        return new WebPush([
            'VAPID' => [
                'subject' => (string) config('chat_pwa.vapid.subject'),
                'publicKey' => (string) config('chat_pwa.vapid.public_key'),
                'privateKey' => (string) config('chat_pwa.vapid.private_key'),
            ],
        ]);
    }
}
