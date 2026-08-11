<?php

declare(strict_types=1);

namespace App\Support;

use App\Events\WhatsAppConversationUpdated;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Services\TelnyxWhatsAppService;
use Illuminate\Support\Facades\Log;
use Throwable;

final class WhatsAppConversationSync
{
    public static function syncFromMessage(WhatsAppMessage $message, bool $broadcast = true): WhatsAppConversation
    {
        $conversation = WhatsAppConversation::query()->firstOrNew([
            'phone' => $message->phone,
        ]);

        if (! $conversation->exists) {
            $conversation->status = WhatsAppConversation::STATUS_OPEN;
        }

        $sentAt = $message->sent_at ?? $message->created_at ?? now();

        $shouldUpdatePreview = ! $conversation->exists
            || $conversation->last_message_at === null
            || $sentAt->greaterThanOrEqualTo($conversation->last_message_at);

        if ($shouldUpdatePreview) {
            $conversation->last_message_body = $message->body;
            $conversation->last_message_direction = $message->direction;
            $conversation->last_message_is_note = (bool) $message->is_note;
            $conversation->last_message_at = $sentAt;
        }

        if ($message->trainee_id) {
            $conversation->trainee_id = $message->trainee_id;
        } elseif (! $conversation->trainee_id) {
            WhatsAppTraineeLinker::attachTraineeIfMissing($conversation);
        }

        if (
            $message->direction === WhatsAppMessage::DIRECTION_INBOUND
            && ! $message->is_note
        ) {
            $conversation->status = WhatsAppConversation::STATUS_OPEN;
            $conversation->has_unread = true;
            if (
                $conversation->last_inbound_at === null
                || $sentAt->greaterThan($conversation->last_inbound_at)
            ) {
                $conversation->last_inbound_at = $sentAt;
            }
        }

        $conversation->save();

        if ($broadcast) {
            self::broadcast($conversation);
        }

        return $conversation;
    }

    public static function broadcast(WhatsAppConversation $conversation): void
    {
        $driver = (string) config('broadcasting.default');

        if (! in_array($driver, ['pusher', 'redis', 'log'], true)) {
            return;
        }

        try {
            PusherSocketId::normalizeRequest();

            $payload = $conversation->fresh([
                'agents:id,name',
                'tags:id,name,color',
                'trainee.company:id,name_ar',
            ]) ?? $conversation;

            $pending = broadcast(WhatsAppConversationUpdated::fromModel($payload))->toOthers();
            unset($pending);
        } catch (Throwable $exception) {
            Log::error('WhatsApp conversation broadcast failed', [
                'conversation_id' => $conversation->id,
                'phone' => $conversation->phone,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Attach the authenticated user as an agent on the conversation for this phone.
     */
    public static function assignCurrentUser(string $phone, ?string $userId = null): ?WhatsAppConversation
    {
        $normalizedPhone = app(TelnyxWhatsAppService::class)->normalizePhoneDigits($phone);
        $agentId = $userId ?: auth()->id();

        if ($normalizedPhone === '' || ! $agentId) {
            return null;
        }

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone' => $normalizedPhone],
            ['status' => WhatsAppConversation::STATUS_OPEN]
        );

        if ($conversation->agents()->where('users.id', $agentId)->exists()) {
            return $conversation->loadMissing([
                'agents:id,name',
                'tags:id,name,color',
                'trainee.company:id,name_ar',
            ]);
        }

        $conversation->agents()->attach($agentId, [
            'assigned_at' => now(),
        ]);

        $conversation->load([
            'agents:id,name',
            'tags:id,name,color',
            'trainee.company:id,name_ar',
        ]);

        self::broadcast($conversation);

        return $conversation;
    }
}