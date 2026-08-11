<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Crypt;
use Throwable;

final class WhatsAppAiSettings
{
    public const KEY_ENABLED = 'whatsapp_ai_enabled';

    public const KEY_OPENAI_KEY = 'whatsapp_ai_openai_key';

    public const KEY_MODEL = 'whatsapp_ai_model';

    public const KEY_SYSTEM_PROMPT = 'whatsapp_ai_system_prompt';

    public const KEY_PURPOSE = 'whatsapp_ai_purpose';

    public const KEY_TONE = 'whatsapp_ai_tone';

    public const KEY_HANDOFF_RULES = 'whatsapp_ai_handoff_rules';

    public const KEY_MAX_REPLY_CHARS = 'whatsapp_ai_max_reply_chars';

    public const DEFAULT_MODEL = 'gpt-4o-mini';

    public const DEFAULT_MAX_REPLY_CHARS = 800;

    public const DEFAULT_SYSTEM_PROMPT = 'You are a WhatsApp support assistant for a training company. Answer only from tool results. Be concise. Do not invent facts about contracts, invoices, or account status.';

    public const DEFAULT_PURPOSE = 'Help trainees check: (1) whether their training contract is signed/active, (2) whether their account is suspended, (3) whether they have pending invoices, and share a payment link when asked. Escalate anything else to a human agent.';

    public const DEFAULT_TONE = 'Professional Arabic; short WhatsApp-friendly replies.';

    public const DEFAULT_HANDOFF_RULES = 'If the account is suspended or blocked, call request_human_agent immediately (do not only describe the status). If the question is outside contract/account/invoices, or tools return not found / error, call request_human_agent, briefly tell the trainee a colleague will follow up, and stop inventing answers. Never claim you transferred them without calling request_human_agent.';

    public static function isEnabled(): bool
    {
        return self::getRaw(self::KEY_ENABLED, '0') === '1';
    }

    public static function hasApiKey(): bool
    {
        return self::getApiKey() !== '';
    }

    public static function isReady(): bool
    {
        return self::isEnabled() && self::hasApiKey();
    }

    public static function getApiKey(): string
    {
        $encrypted = self::getRaw(self::KEY_OPENAI_KEY, '');
        if ($encrypted === '') {
            return '';
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (Throwable) {
            return '';
        }
    }

    public static function getModel(): string
    {
        $model = trim(self::getRaw(self::KEY_MODEL, self::DEFAULT_MODEL));

        return $model !== '' ? $model : self::DEFAULT_MODEL;
    }

    public static function getSystemPrompt(): string
    {
        return self::getRaw(self::KEY_SYSTEM_PROMPT, self::DEFAULT_SYSTEM_PROMPT);
    }

    public static function getPurpose(): string
    {
        return self::getRaw(self::KEY_PURPOSE, self::DEFAULT_PURPOSE);
    }

    public static function getTone(): string
    {
        return self::getRaw(self::KEY_TONE, self::DEFAULT_TONE);
    }

    public static function getHandoffRules(): string
    {
        return self::getRaw(self::KEY_HANDOFF_RULES, self::DEFAULT_HANDOFF_RULES);
    }

    public static function getMaxReplyChars(): int
    {
        $value = (int) self::getRaw(self::KEY_MAX_REPLY_CHARS, (string) self::DEFAULT_MAX_REPLY_CHARS);

        return $value > 0 ? $value : self::DEFAULT_MAX_REPLY_CHARS;
    }

    /**
     * Public payload for the settings UI (API key never returned in full).
     *
     * @return array{
     *     enabled: bool,
     *     openai_key_masked: string|null,
     *     openai_key_set: bool,
     *     model: string,
     *     system_prompt: string,
     *     purpose: string,
     *     tone: string,
     *     handoff_rules: string,
     *     max_reply_chars: int
     * }
     */
    public static function forAdmin(): array
    {
        $key = self::getApiKey();

        return [
            'enabled' => self::isEnabled(),
            'openai_key_masked' => $key !== '' ? self::maskApiKey($key) : null,
            'openai_key_set' => $key !== '',
            'model' => self::getModel(),
            'system_prompt' => self::getSystemPrompt(),
            'purpose' => self::getPurpose(),
            'tone' => self::getTone(),
            'handoff_rules' => self::getHandoffRules(),
            'max_reply_chars' => self::getMaxReplyChars(),
        ];
    }

    /**
     * @param  array{
     *     enabled: bool,
     *     openai_key?: string|null,
     *     model: string,
     *     system_prompt: string,
     *     purpose: string,
     *     tone: string,
     *     handoff_rules: string,
     *     max_reply_chars: int
     * }  $data
     */
    public static function save(array $data): void
    {
        self::put(self::KEY_ENABLED, ! empty($data['enabled']) ? '1' : '0');
        self::put(self::KEY_MODEL, (string) $data['model']);
        self::put(self::KEY_SYSTEM_PROMPT, (string) $data['system_prompt']);
        self::put(self::KEY_PURPOSE, (string) $data['purpose']);
        self::put(self::KEY_TONE, (string) $data['tone']);
        self::put(self::KEY_HANDOFF_RULES, (string) $data['handoff_rules']);
        self::put(self::KEY_MAX_REPLY_CHARS, (string) (int) $data['max_reply_chars']);

        $newKey = trim((string) ($data['openai_key'] ?? ''));
        if ($newKey !== '') {
            self::put(self::KEY_OPENAI_KEY, Crypt::encryptString($newKey));
        }
    }

    public static function maskApiKey(string $key): string
    {
        $len = strlen($key);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }

        return substr($key, 0, 3) . '…' . substr($key, -4);
    }

    public static function composeSystemMessage(): string
    {
        $parts = [
            trim(self::getSystemPrompt()),
            'Purpose: ' . trim(self::getPurpose()),
            'Tone / language: ' . trim(self::getTone()),
            'Handoff rules: ' . trim(self::getHandoffRules()),
            'Hard safety rules (always apply):',
            '- Only state facts returned by tools; never invent contract, invoice, or suspension details.',
            '- Never expose or discuss another trainee\'s data.',
            '- Never ask for card numbers or payment credentials; only share payment links from tools.',
            '- The caller phone is already known; do not ask the model to supply a different identity phone.',
            '- When handoff rules apply, the account is suspended/blocked, or you cannot help, you MUST call the request_human_agent tool before any handoff wording. Never write that you transferred the trainee without calling that tool.',
        ];

        return implode("\n\n", array_filter($parts));
    }

    private static function getRaw(string $name, string $default): string
    {
        $value = AppSetting::query()->where('name', $name)->value('value');

        if ($value === null || $value === '') {
            return $default;
        }

        return (string) $value;
    }

    private static function put(string $name, string $value): void
    {
        AppSetting::query()->updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );
    }
}
