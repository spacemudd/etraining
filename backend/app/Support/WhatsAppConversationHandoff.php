<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppTag;
use App\Services\TelnyxWhatsAppService;
use Throwable;

final class WhatsAppConversationHandoff
{
    public const NEED_HUMAN_AGENT_TAG = 'need_human_agent';

    public const NEED_HUMAN_AGENT_COLOR = '#DC2626';

    /**
     * Tag the conversation for human follow-up and pause the bot indefinitely.
     *
     * @return array{ok: bool, tag: string, conversation_id: string|null}
     */
    public static function requestHumanAgent(string $phone, ?string $reason = null): array
    {
        $service = app(TelnyxWhatsAppService::class);
        $normalizedPhone = $service->normalizePhoneDigits($phone);

        if ($normalizedPhone === '') {
            return [
                'ok' => false,
                'tag' => self::NEED_HUMAN_AGENT_TAG,
                'conversation_id' => null,
            ];
        }

        WhatsAppBotPause::pauseIndefinitely($normalizedPhone);

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone' => $normalizedPhone],
            ['status' => WhatsAppConversation::STATUS_OPEN]
        );

        // Keep in the open queue so agents see it with the need_human_agent tag.
        $conversation->status = WhatsAppConversation::STATUS_OPEN;
        $conversation->save();

        $tag = self::ensureNeedHumanAgentTag();
        $conversation->tags()->syncWithoutDetaching([$tag->id]);

        try {
            $conversation->load([
                'agents:id,name',
                'tags:id,name,color',
                'trainee.company:id,name_ar',
            ]);
            WhatsAppConversationSync::broadcast($conversation);
        } catch (Throwable $exception) {
            // Broadcast/relations may be unavailable in some environments (e.g. unit tests).
        }

        return [
            'ok' => true,
            'tag' => self::NEED_HUMAN_AGENT_TAG,
            'conversation_id' => (string) $conversation->id,
            'reason' => $reason,
            'message' => 'Conversation tagged need_human_agent and bot paused for a human agent.',
        ];
    }

    public static function ensureNeedHumanAgentTag(): WhatsAppTag
    {
        return WhatsAppTag::query()->firstOrCreate(
            ['name' => self::NEED_HUMAN_AGENT_TAG],
            ['color' => self::NEED_HUMAN_AGENT_COLOR]
        );
    }
}
