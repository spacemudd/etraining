<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Back\WhatsAppMessage;
use App\Models\PushSubscription;
use App\Services\ChatWebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppChatPushNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $messageId)
    {
    }

    public function handle(ChatWebPushService $pushService): void
    {
        if (! $pushService->isConfigured()) {
            return;
        }

        $message = WhatsAppMessage::query()->find($this->messageId);
        if (! $message) {
            return;
        }

        if (
            $message->direction !== WhatsAppMessage::DIRECTION_INBOUND
            || $message->is_note
        ) {
            return;
        }

        $body = trim((string) ($message->body ?? ''));
        if ($body === '') {
            $body = 'New message';
        }
        if (mb_strlen($body) > 120) {
            $body = mb_substr($body, 0, 117).'...';
        }

        $payload = [
            'title' => 'New WhatsApp message',
            'body' => $body,
            'url' => '/back/chat',
        ];

        $subscriptions = PushSubscription::query()
            ->whereHas('user', function ($query): void {
                $query->permission('access-whatsapp-chats');
            })
            ->get();

        foreach ($subscriptions as $subscription) {
            $pushService->sendToSubscription($subscription, $payload);
        }
    }
}
