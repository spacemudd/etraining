<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Back\WhatsAppMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhatsAppMessageReceived implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var array<string, mixed>
     */
    public array $message;

    public function __construct(WhatsAppMessage $message)
    {
        $metadata = is_array($message->metadata) ? $message->metadata : [];
        $isBot = ! empty($metadata['is_bot']);

        $this->message = $message->withPersistedMedia([
            'id' => $message->id,
            'sid' => $message->twilio_sid ?: $message->id,
            'phone' => $message->phone,
            'body' => $message->body ?? '',
            'status' => $message->status,
            'direction' => $message->direction === WhatsAppMessage::DIRECTION_OUTBOUND
                ? 'outbound-api'
                : 'inbound',
            'is_note' => (bool) $message->is_note,
            'is_bot' => $isBot,
            'from' => $message->from_address,
            'to' => $message->to_address,
            'date_sent' => optional($message->sent_at)->toIso8601String(),
            'error_message' => $metadata['error_message'] ?? null,
            'metadata' => $metadata,
            'trainee_id' => $message->trainee_id,
            'author' => $isBot
                ? [
                    'id' => null,
                    'name' => 'Bot',
                    'is_bot' => true,
                ]
                : null,
        ]);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('whatsapp-chat');
    }

    public function broadcastAs(): string
    {
        return 'WhatsAppMessageReceived';
    }
}
