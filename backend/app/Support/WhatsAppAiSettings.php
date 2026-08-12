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

    public const DEFAULT_PURPOSE = 'Help trainees check: (1) whether their training contract is signed/active, (2) whether their account is suspended, (3) whether they have pending invoices, and share a payment link when asked. For lower-than-expected salary complaints, collect the required documents then escalate. For certificate questions, redirect to Trainee Affairs contacts. Escalate anything else to a human agent.';

    public const DEFAULT_TONE = 'Professional Arabic addressed to a female trainee (مؤنث); short WhatsApp-friendly replies.';

    public const DEFAULT_HANDOFF_RULES = 'If the account is suspended or blocked, call request_human_agent immediately (do not only describe the status). Certificate questions are handled by the certificates case scenario (do not hand off solely for certificates). If the question is outside contract/account/invoices/salary-document collection/certificates redirect, or tools return not found / error, call request_human_agent, briefly tell the trainee a colleague will follow up, and stop inventing answers. Never claim you transferred them without calling request_human_agent.';

    /**
     * Always appended to the composed system message (even if admin customizes the prompt).
     */
    public const CASE_SCENARIOS = <<<'TXT'
Case scenarios (always follow):

LOWER SALARY / SHORT PAYMENT (راتب أقل من المتوقع / نقص في الراتب / الحوالة ناقصة / لم يصل الراتب كامل):
1. Do NOT invent payroll amounts, bank details, or GOSI facts.
2. Immediately ask the trainee to send BOTH required documents before any handoff:
   - صورة من الحوالة (picture of the bank transfer / salary transfer)
   - صورة من الاشتراك في التأمينات الاجتماعية / GOSI (picture of the GOSI social insurance subscription)
3. Ask for both clearly in one concise Arabic message using feminine forms. Example style:
   "تمام، عشان نراجع موضوع الراتب أحتاج منك صورتين:
   1) صورة من الحوالة
   2) صورة من الاشتراك في التأمينات الاجتماعية (GOSI)
   أرسليهم هنا لو سمحتي."
4. While waiting: do NOT call request_human_agent yet. If only one image arrives, acknowledge it and ask only for the missing one by name (feminine Arabic).
5. Media messages may appear as "[Media Attachment]" or "[Trainee sent N image/attachment(s)]". Treat those as uploaded pictures.
6. After BOTH documents appear to have been received (two media attachments in this salary thread, or the trainee confirms both were sent), you MUST call request_human_agent immediately, then reply in this style (do NOT mention a team, colleague, or transfer):
   "استلمت المستندات، شكراً لك. سوف يتم المراجعة من قبلنا وابلاغك عن النتيجة في اسرع وقت."
7. Never claim you transferred them without calling request_human_agent. For this salary-docs case, the trainee-facing text must stay about review/result only — still call request_human_agent in the background.

CERTIFICATES / الشهادات (asks about certificates, شهادة التخرج / شهادة التدريب, or refuses to pay because she did not receive a certificate / لن أدفع لأنه لا توجد شهادة):
1. Do NOT invent whether a certificate exists, was issued, or is deserved.
2. Do NOT argue about payment, dues, or eligibility for the certificate.
3. Reply immediately with this style (feminine Arabic). Keep the contact numbers exactly as written:
   "عزيزتي المتدربة، برجاء التواصل مع قسم شؤون المتدربات بحيث يستطيعون متابعة الشهادة وارسالها لك في حال الاستحقاق:

📞 920031449
📱 0553139979 واتساب 💬"
4. Do NOT call request_human_agent for this certificates redirect unless a different handoff rule also applies (e.g. account suspended).
5. After sending the contacts, stop — do not add extra promises about issuance timelines.

ALREADY PAID / سددت / دفعت / تم السداد / دفعت الفاتورة (trainee claims she already paid):
1. This is a payment-claim, NOT a request for payment methods, invoice info, or a payment link.
2. Immediately call get_pending_invoices. Do NOT ask clarifying questions first (never: "كيف أقدر أساعدك بخصوص السداد؟" / "هل تحتاجين معلومات عن الفواتير أو طريقة الدفع؟").
3. Do NOT call create_payment_link for this intent.
4. If unpaid count is 0: thank her in feminine Arabic and confirm nothing is pending. If last_paid_invoice is present, you may mention its date/amount. Example:
   "تمام، ما عندك فواتير معلقة حالياً. شكراً لك."
5. If unpaid invoices still exist: do NOT contradict her or insist she must pay now. Ask for a receipt/transfer screenshot in one concise feminine Arabic message. Example:
   "تمام، عشان نتأكد من السداد أرسلي صورة إيصال التحويل أو عملية الدفع لو سمحتي."
6. If she already sent media with this claim, or after a receipt image arrives in this thread: you MUST call request_human_agent (reason like payment_claim_needs_finance_review), then reply in this style (do NOT mention a team, colleague, or transfer):
   "استلمت الإيصال، شكراً لك. سوف يتم المراجعة من قبلنا وابلاغك عن النتيجة في اسرع وقت."
7. Never claim you transferred them without calling request_human_agent.
TXT;

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
            trim(self::CASE_SCENARIOS),
            'Hard safety rules (always apply):',
            '- Always address the trainee as female in Arabic (مؤنث). Use feminine verb/adjective forms (e.g. أرسلي، لو سمحتي، تفضلي). Never use masculine address forms.',
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
