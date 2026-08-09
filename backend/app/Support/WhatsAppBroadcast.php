<?php

declare(strict_types=1);

namespace App\Support;

use App\Events\WhatsAppMessageReceived;
use App\Jobs\ProcessWhatsAppBotReply;
use App\Models\Back\WhatsAppMessage;
use Illuminate\Support\Facades\Log;
use Throwable;

final class WhatsAppBroadcast
{
    public static function messageStored(WhatsAppMessage $message): void
    {
        WhatsAppConversationSync::syncFromMessage($message, true);

        if (
            $message->direction === WhatsAppMessage::DIRECTION_INBOUND
            && ! $message->is_note
        ) {
            $metadata = is_array($message->metadata) ? $message->metadata : [];
            if (empty($metadata['is_bot'])) {
                ProcessWhatsAppBotReply::dispatch($message->id);
            }
        }

        $driver = (string) config('broadcasting.default');
        if (! in_array($driver, ['pusher', 'redis', 'log'], true)) {
            Log::warning('WhatsApp broadcast skipped: BROADCAST_DRIVER is not configured for realtime', [
                'driver' => $driver,
                'message_id' => $message->id,
            ]);

            return;
        }

        try {
            broadcast(new WhatsAppMessageReceived($message))->toOthers();

            Log::info('WhatsApp message broadcasted', [
                'driver' => $driver,
                'message_id' => $message->id,
                'phone' => $message->phone,
                'direction' => $message->direction,
                'host' => config('broadcasting.connections.pusher.options.host'),
            ]);
        } catch (Throwable $exception) {
            Log::error('WhatsApp message broadcast failed', [
                'driver' => $driver,
                'message_id' => $message->id,
                'phone' => $message->phone,
                'error' => $exception->getMessage(),
                'host' => config('broadcasting.connections.pusher.options.host'),
                'port' => config('broadcasting.connections.pusher.options.port'),
                'scheme' => config('broadcasting.connections.pusher.options.scheme'),
            ]);
        }
    }
}
