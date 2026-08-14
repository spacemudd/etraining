<?php

declare(strict_types=1);

namespace App\Support;

use App\Events\WhatsAppMessageReceived;
use App\Jobs\ProcessWhatsAppBotReply;
use App\Jobs\SendWhatsAppChatPushNotification;
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
                // Run after the HTTP response so inbound Echo events flush first,
                // then the bot reply is stored/broadcast as a separate event.
                ProcessWhatsAppBotReply::dispatch($message->id)->afterResponse();
            }

            SendWhatsAppChatPushNotification::dispatch($message->id)->afterResponse();
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
            PusherSocketId::normalizeRequest();

            // Webhooks/queue workers have no Echo socket; always notify all subscribers.
            // Agent-authored sends still use toOthers so the sender (who already merged
            // the axios response) does not get a duplicate event.
            $pending = broadcast(new WhatsAppMessageReceived($message));
            $metadata = is_array($message->metadata) ? $message->metadata : [];
            $isBot = ! empty($metadata['is_bot']);
            $isOutbound = $message->direction === WhatsAppMessage::DIRECTION_OUTBOUND;

            if ($isOutbound && ! $isBot && ! $message->is_note) {
                $pending->toOthers();
            }

            // PendingBroadcast dispatches in __destruct — force it inside try/catch.
            unset($pending);

            Log::info('WhatsApp message broadcasted', [
                'driver' => $driver,
                'message_id' => $message->id,
                'phone' => $message->phone,
                'direction' => $message->direction,
                'is_bot' => $isBot,
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
