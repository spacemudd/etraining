<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Support\WhatsAppAiSettings;
use App\Support\WhatsAppBotPause;
use App\Support\WhatsAppConversationHandoff;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppAiBotService
{
    private const MAX_TOOL_ROUNDS = 4;

    private const HISTORY_LIMIT = 12;

    /**
     * Fixed reply when account status is suspended (not invented by the model).
     */
    public const SUSPENDED_HANDOFF_REPLY = 'حسابك موقوف حالياً، حوّلت موضوعك لزميلي عشان يكمّل معك.';

    /**
     * Phrases that claim a human handoff already happened.
     *
     * @var list<string>
     */
    private const HANDOFF_CLAIM_PHRASES = [
        'حوّلت موضوعك',
        'حولت موضوعك',
        'حوّلت طلبك',
        'حولت طلبك',
        'زميلي',
    ];

    public function __construct(
        private readonly TelnyxWhatsAppService $whatsAppService,
        private readonly WhatsAppAiTraineeTools $tools
    ) {
    }

    public function handleInbound(WhatsAppMessage $message): void
    {
        if ($message->direction !== WhatsAppMessage::DIRECTION_INBOUND || $message->is_note) {
            return;
        }

        $metadata = is_array($message->metadata) ? $message->metadata : [];
        if (! empty($metadata['is_bot'])) {
            return;
        }

        if (! WhatsAppAiSettings::isReady()) {
            return;
        }

        $phone = $this->whatsAppService->normalizePhoneDigits((string) $message->phone);
        if ($phone === '') {
            return;
        }

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone' => $phone],
            ['status' => WhatsAppConversation::STATUS_OPEN]
        );

        if (WhatsAppBotPause::isPaused($conversation)) {
            Log::info('WhatsApp AI bot skipped: conversation paused', [
                'phone' => $phone,
                'paused_until' => optional($conversation->bot_paused_until)->toIso8601String(),
            ]);

            return;
        }

        if (WhatsAppBotPause::pauseExpired($conversation)) {
            $conversation->bot_paused_until = null;
            $conversation->save();
        }

        try {
            $reply = $this->generateReply($phone, $message);
            if ($reply === null || trim($reply) === '') {
                return;
            }

            $reply = $this->truncate($reply, WhatsAppAiSettings::getMaxReplyChars());
            $this->maybeHandoffFromReplyPhrase($phone, $reply);
            $this->sendReply($phone, $reply, $message->trainee_id ? (string) $message->trainee_id : null);
        } catch (Throwable $exception) {
            Log::error('WhatsApp AI bot failed', [
                'phone' => $phone,
                'message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function generateReply(string $phone, WhatsAppMessage $inbound): ?string
    {
        $apiKey = WhatsAppAiSettings::getApiKey();
        $messages = [
            [
                'role' => 'system',
                'content' => WhatsAppAiSettings::composeSystemMessage(),
            ],
        ];

        foreach ($this->recentHistory($phone, (string) $inbound->id) as $row) {
            $messages[] = $row;
        }

        $messages[] = [
            'role' => 'user',
            'content' => $this->formatUserMessageContent($inbound),
        ];

        $tools = $this->tools->openAiToolDefinitions();

        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
            $response = Http::withToken($apiKey)
                ->timeout(45)
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => WhatsAppAiSettings::getModel(),
                    'messages' => $messages,
                    'tools' => $tools,
                    'tool_choice' => 'auto',
                    'temperature' => 0.2,
                ]);

            if (! $response->successful()) {
                Log::warning('WhatsApp AI OpenAI HTTP error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $choice = $response->json('choices.0.message');
            if (! is_array($choice)) {
                return null;
            }

            $toolCalls = $choice['tool_calls'] ?? null;
            if (! is_array($toolCalls) || $toolCalls === []) {
                $content = trim((string) ($choice['content'] ?? ''));

                return $content !== '' ? $content : null;
            }

            $messages[] = [
                'role' => 'assistant',
                'content' => $choice['content'] ?? null,
                'tool_calls' => $toolCalls,
            ];

            foreach ($toolCalls as $toolCall) {
                if (! is_array($toolCall)) {
                    continue;
                }

                $function = is_array($toolCall['function'] ?? null) ? $toolCall['function'] : [];
                $name = (string) ($function['name'] ?? '');
                $rawArgs = (string) ($function['arguments'] ?? '{}');
                $arguments = json_decode($rawArgs, true);
                if (! is_array($arguments)) {
                    $arguments = [];
                }

                $result = $this->tools->call($name, $arguments, $phone);

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => (string) ($toolCall['id'] ?? ''),
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ];

                if ($this->shouldAutoHandoffForSuspended($name, $result)) {
                    $this->tools->call(
                        'request_human_agent',
                        ['reason' => 'account_suspended'],
                        $phone
                    );

                    return self::SUSPENDED_HANDOFF_REPLY;
                }
            }
        }

        // Final pass without tools if we exhausted tool rounds.
        $response = Http::withToken($apiKey)
            ->timeout(45)
            ->acceptJson()
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => WhatsAppAiSettings::getModel(),
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

        if (! $response->successful()) {
            return null;
        }

        $content = trim((string) $response->json('choices.0.message.content'));

        return $content !== '' ? $content : null;
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function shouldAutoHandoffForSuspended(string $toolName, array $result): bool
    {
        if ($toolName !== 'get_account_status') {
            return false;
        }

        return ! empty($result['ok'])
            && ! empty($result['found'])
            && ! empty($result['is_suspended']);
    }

    private function maybeHandoffFromReplyPhrase(string $phone, string $reply): void
    {
        if (! $this->replyClaimsHandoff($reply)) {
            return;
        }

        if ($this->conversationAlreadyNeedsHumanAgent($phone)) {
            return;
        }

        $this->tools->call(
            'request_human_agent',
            ['reason' => 'handoff_phrase_detected'],
            $phone
        );
    }

    private function replyClaimsHandoff(string $reply): bool
    {
        foreach (self::HANDOFF_CLAIM_PHRASES as $phrase) {
            if (mb_stripos($reply, $phrase) !== false) {
                return true;
            }
        }

        return false;
    }

    private function conversationAlreadyNeedsHumanAgent(string $phone): bool
    {
        $conversation = WhatsAppConversation::query()->where('phone', $phone)->first();
        if (! $conversation) {
            return false;
        }

        return $conversation->tags()
            ->where('whatsapp_tags.name', WhatsAppConversationHandoff::NEED_HUMAN_AGENT_TAG)
            ->exists();
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    private function recentHistory(string $phone, string $excludeMessageId): array
    {
        $rows = WhatsAppMessage::query()
            ->where('phone', $phone)
            ->where('is_note', false)
            ->where('id', '!=', $excludeMessageId)
            ->orderByDesc('created_at')
            ->limit(self::HISTORY_LIMIT)
            ->get(['direction', 'body', 'metadata'])
            ->reverse()
            ->values();

        $history = [];
        foreach ($rows as $row) {
            $meta = is_array($row->metadata) ? $row->metadata : [];
            $isBot = ! empty($meta['is_bot']);
            $isOutbound = $row->direction === WhatsAppMessage::DIRECTION_OUTBOUND;

            if ($isOutbound && $isBot) {
                $body = trim((string) ($row->body ?? ''));
                if ($body !== '') {
                    $history[] = ['role' => 'assistant', 'content' => $body];
                }
            } elseif (! $isOutbound) {
                $content = $this->formatUserMessageContent($row);
                if ($content !== '') {
                    $history[] = ['role' => 'user', 'content' => $content];
                }
            }
            // Skip human-agent outbound from the model history to avoid confusing roles.
        }

        return $history;
    }

    private function formatUserMessageContent(WhatsAppMessage $message): string
    {
        $body = trim((string) ($message->body ?? ''));
        $meta = is_array($message->metadata) ? $message->metadata : [];
        $media = is_array($meta['media'] ?? null) ? $meta['media'] : [];
        $mediaCount = count($media);

        if ($mediaCount > 0) {
            $label = sprintf('[Trainee sent %d image/attachment(s)]', $mediaCount);
            if ($body === '' || $body === '[Media Attachment]') {
                return $label;
            }

            return $body . "\n" . $label;
        }

        return $body;
    }

    private function sendReply(string $phone, string $body, ?string $traineeId): void
    {
        if (filled(config('telnyx.api_key'))) {
            $this->whatsAppService->sendBotFreeformMessage($phone, $body, $traineeId);

            return;
        }

        $this->whatsAppService->storeLocalBotMessage($phone, $body, $traineeId);
    }

    private function truncate(string $text, int $maxChars): string
    {
        if (mb_strlen($text) <= $maxChars) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, max(1, $maxChars - 1))) . '…';
    }
}
