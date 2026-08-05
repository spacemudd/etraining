<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\WhatsAppMessageReceived;
use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppMessage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TelnyxWhatsAppService
{
    private const API_BASE = 'https://api.telnyx.com/v2';

    private ?Client $client = null;

    private function client(): Client
    {
        if ($this->client === null) {
            $this->client = new Client([
                'base_uri' => self::API_BASE . '/',
                'headers' => [
                    'Authorization' => 'Bearer ' . config('telnyx.api_key'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
            ]);
        }

        return $this->client;
    }

    public function isConfigured(): bool
    {
        return filled(config('telnyx.api_key'))
            && filled(config('telnyx.whatsapp_from'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listTemplates(): array
    {
        $query = ['page[size]' => 100];

        if (filled(config('telnyx.waba_id'))) {
            $query['filter[waba_id]'] = config('telnyx.waba_id');
        }

        $payload = $this->request('GET', 'whatsapp/message_templates', [
            'query' => $query,
        ], 'Failed to fetch WhatsApp templates.');

        $templates = [];

        foreach ($payload['data'] ?? [] as $template) {
            $templates[] = $this->formatTemplate($template);
        }

        return $templates;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTemplate(string $templateId): array
    {
        $payload = $this->request(
            'GET',
            'whatsapp/message_templates/' . $templateId,
            [],
            'Failed to fetch WhatsApp template.'
        );

        return $this->formatTemplate($payload['data'] ?? $payload);
    }

    /**
     * @param  array<string, string>  $contentVariables
     * @return array<string, mixed>
     */
    public function sendTemplate(string $phone, string $templateId, array $contentVariables = [], ?string $traineeId = null): array
    {
        $template = $this->getTemplate($templateId);
        $components = $this->buildTemplateComponents($contentVariables);

        $templatePayload = ['components' => $components];

        if (! empty($template['friendly_name']) && ! empty($template['language'])) {
            $templatePayload['name'] = $template['friendly_name'];
            $templatePayload['language'] = [
                'policy' => 'deterministic',
                'code' => $template['language'],
            ];
        } elseif ($templateId !== '') {
            $templatePayload['template_id'] = $templateId;
        }

        $message = $this->sendWhatsAppMessage($phone, [
            'type' => 'template',
            'template' => $templatePayload,
        ], $this->previewTemplateBody($template, $contentVariables));
        $this->storeOutboundMessage($message, $phone, $traineeId);

        return $message;
    }

    /**
     * @return array<string, mixed>
     */
    public function sendFreeformMessage(string $phone, string $body, ?string $traineeId = null): array
    {
        $message = $this->sendWhatsAppMessage($phone, [
            'type' => 'text',
            'text' => [
                'body' => $body,
                'preview_url' => false,
            ],
        ], $body);

        $this->storeOutboundMessage($message, $phone, $traineeId);

        return $message;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listMessages(string $phone, int $limit = 30, ?string $since = null): array
    {
        $normalizedPhone = $this->normalizePhoneDigits($phone);
        $query = WhatsAppMessage::query()
            ->where('phone', $normalizedPhone)
            ->orderBy('sent_at')
            ->orderBy('created_at');

        if ($since) {
            $query->where('sent_at', '>', Carbon::parse($since));
        }

        return $query->limit($limit)
            ->get()
            ->map(fn (WhatsAppMessage $message) => $this->formatStoredMessage($message))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function storeInboundMessage(array $payload): WhatsAppMessage
    {
        $from = (string) ($payload['from'] ?? '');
        $phone = $this->normalizePhoneDigits($from);

        $message = WhatsAppMessage::query()->create([
            'twilio_sid' => $payload['external_id'] ?? null,
            'trainee_id' => $this->findTraineeByPhone($phone)?->id,
            'phone' => $phone,
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'body' => $payload['body'] ?? '',
            'status' => $payload['status'] ?? 'received',
            'from_address' => $from,
            'to_address' => $payload['to'] ?? null,
            'sent_at' => $payload['sent_at'] ?? now(),
            'metadata' => $payload['metadata'] ?? null,
        ]);

        broadcast(new WhatsAppMessageReceived($message));

        return $message;
    }

    /**
     * @param  array<string, mixed>  $message
     */
    public function storeOutboundMessage(array $message, string $phone, ?string $traineeId = null): WhatsAppMessage
    {
        $normalizedPhone = $this->normalizePhoneDigits($phone);
        $trainee = $traineeId
            ? Trainee::query()->find($traineeId)
            : $this->findTraineeByPhone($normalizedPhone);

        $attributes = [
            'trainee_id' => $trainee?->id,
            'phone' => $normalizedPhone,
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'body' => $message['body'] ?? '',
            'status' => $message['status'] ?? null,
            'from_address' => $message['from'] ?? null,
            'to_address' => $message['to'] ?? null,
            'sent_at' => isset($message['date_sent']) ? Carbon::parse($message['date_sent']) : now(),
        ];

        if (! empty($message['sid'])) {
            $stored = WhatsAppMessage::query()->updateOrCreate(
                ['twilio_sid' => $message['sid']],
                $attributes
            );
        } else {
            $stored = WhatsAppMessage::query()->create(array_merge($attributes, [
                'twilio_sid' => null,
            ]));
        }

        broadcast(new WhatsAppMessageReceived($stored));

        return $stored;
    }

    public function updateMessageStatus(string $externalId, string $status, ?string $errorMessage = null): void
    {
        $message = WhatsAppMessage::query()->where('twilio_sid', $externalId)->first();

        if (! $message) {
            return;
        }

        $metadata = $message->metadata ?? [];

        if ($errorMessage) {
            $metadata['error_message'] = $errorMessage;
        }

        $message->update([
            'status' => $status,
            'metadata' => $metadata !== [] ? $metadata : null,
        ]);
    }

    public function normalizePhoneDigits(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', str_replace('whatsapp:', '', $phone)) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '05')) {
            $digits = '966' . substr($digits, 1);
        }

        if (str_starts_with($digits, '5') && strlen($digits) === 9) {
            $digits = '966' . $digits;
        }

        // Ensure the number starts with a plus sign and the country code
        if (!str_starts_with($digits, '+')) {
            $digits = '+' . $digits;
        }

        return $digits;
    }

    public function findTraineeByPhone(string $normalizedPhone): ?Trainee
    {
        $suffix = substr($normalizedPhone, -9);

        if ($suffix === '' || $suffix === false) {
            return null;
        }

        return Trainee::query()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->where(function ($query) use ($normalizedPhone, $suffix) {
                $query->where('phone', 'LIKE', '%' . $normalizedPhone . '%')
                    ->orWhere('phone', 'LIKE', '%' . $suffix);
            })
            ->first();
    }

    // Removed toE164 as Telnyx seems to handle it internally if the number is properly formatted.
    // If issues persist, consider re-introducing or using a dedicated E.164 formatting library.

    /**
     * @param  array<string, mixed>  $whatsappMessage
     * @return array<string, mixed>
     */
    private function sendWhatsAppMessage(string $phone, array $whatsappMessage, string $bodyForStorage = ''): array
    {
        // Ensure the phone number is normalized before sending
        $to = $this->normalizePhoneDigits($phone);
        $from = $this->normalizePhoneDigits(config('telnyx.whatsapp_from'));


        $params = [
            'from' => $from,
            'to' => $to,
            'whatsapp_message' => $whatsappMessage,
        ];

        $messagingProfileId = config('telnyx.messaging_profile_id');
        if (filled($messagingProfileId)) {
            $params['messaging_profile_id'] = $messagingProfileId;
        }

        $webhookUrl = config('telnyx.status_callback_url');

        if (! filled($webhookUrl)) {
            try {
                $webhookUrl = route('webhooks.telnyx.whatsapp');
            } catch (\Throwable) {
                $webhookUrl = null;
            }
        }

        if (filled($webhookUrl)) {
            $params['webhook_url'] = $webhookUrl;
        }

        $payload = $this->request('POST', 'messages/whatsapp', [
            'json' => $params,
        ], 'Failed to send WhatsApp message.');

        $data = $payload['data'] ?? [];

        return [
            'sid' => $data['id'] ?? null,
            'body' => $bodyForStorage,
            'status' => $data['to'][0]['status'] ?? $data['status'] ?? 'queued',
            'direction' => 'outbound-api',
            'from' => $data['from']['phone_number'] ?? $params['from'],
            'to' => $data['to'][0]['phone_number'] ?? $params['to'],
            'date_sent' => now()->toIso8601String(),
            'error_message' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $options = [], string $errorMessage = 'Telnyx API request failed.'): array
    {
        $response = $this->client()->request($method, $uri, $options);
        $payload = json_decode((string) $response->getBody(), true) ?? [];

        if ($response->getStatusCode() >= 400) {
            $context = [
                'method' => $method,
                'uri' => $uri,
                'status' => $response->getStatusCode(),
                'response' => $payload,
            ];

            $fromNumber = $options['json']['from'] ?? null;
            $toNumber = $options['json']['to'] ?? null;

            if ($fromNumber) {
                $context['from_number'] = $fromNumber;
            }
            if ($toNumber) {
                $context['to_number'] = $toNumber;
            }

            Log::error('Telnyx WhatsApp API request failed', $context);

            $errorDetails = $payload['errors'][0]['detail']
                ?? $payload['errors'][0]['title']
                ?? json_encode($payload['errors'] ?? $payload); // Fallback to full errors or payload

            $configuredFrom = config('telnyx.whatsapp_from');
            $apiKeyPrefix = substr(config('telnyx.api_key'), 0, 5) . '...';

            $message = sprintf(
                "Telnyx API request failed with status %d: %s",
                $response->getStatusCode(),
                (string) $errorDetails
            );

            $message .= " (Configured From: {$configuredFrom})";
            $message .= " (Normalized From: {$fromNumber})";
            $message .= " (To: {$toNumber})";
            $message .= " (API Key: {$apiKeyPrefix})";

            throw new RuntimeException($message);
        }

        return is_array($payload) ? $payload : [];
    }

    /**
     * @param  array<string, mixed>  $template
     * @return array<string, mixed>
     */
    private function formatTemplate(array $template): array
    {
        $body = $this->extractTemplateBody($template['components'] ?? []);
        $variables = $this->extractVariableKeys($body);
        $variableSamples = $this->extractVariableSamples($template['components'] ?? []);

        if ($variables === [] && $variableSamples !== []) {
            $variables = array_map('strval', array_keys($variableSamples));
            sort($variables, SORT_NUMERIC);
        }

        return [
            'sid' => $template['id'] ?? '',
            'friendly_name' => $template['name'] ?? '',
            'language' => $template['language'] ?? '',
            'body' => $body,
            'variables' => array_values($variables),
            'variable_samples' => $variableSamples,
            'approval_status' => strtolower((string) ($template['status'] ?? 'unknown')),
            'category' => $template['category'] ?? null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     */
    private function extractTemplateBody(array $components): string
    {
        foreach ($components as $component) {
            if (! is_array($component)) {
                continue;
            }

            if (strtoupper((string) ($component['type'] ?? '')) === 'BODY' && ! empty($component['text'])) {
                return (string) $component['text'];
            }
        }

        return '';
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     * @return array<string, string>
     */
    private function extractVariableSamples(array $components): array
    {
        $samples = [];

        foreach ($components as $component) {
            if (! is_array($component) || strtoupper((string) ($component['type'] ?? '')) !== 'BODY') {
                continue;
            }

            $bodyText = $component['example']['body_text'][0] ?? null;

            if (! is_array($bodyText)) {
                continue;
            }

            foreach (array_values($bodyText) as $index => $value) {
                $samples[(string) ($index + 1)] = (string) $value;
            }
        }

        return $samples;
    }

    /**
     * @return array<int, string>
     */
    private function extractVariableKeys(string $body): array
    {
        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);

        $keys = array_unique($matches[1] ?? []);
        sort($keys, SORT_NUMERIC);

        return array_values($keys);
    }

    /**
     * @param  array<string, string>  $contentVariables
     * @return array<int, array<string, mixed>>
     */
    private function buildTemplateComponents(array $contentVariables): array
    {
        if ($contentVariables === []) {
            return [];
        }

        ksort($contentVariables, SORT_NUMERIC);

        $parameters = [];

        foreach ($contentVariables as $value) {
            $parameters[] = [
                'type' => 'text',
                'text' => (string) $value,
            ];
        }

        return [
            [
                'type' => 'body',
                'parameters' => $parameters,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $template
     * @param  array<string, string>  $contentVariables
     */
    private function previewTemplateBody(array $template, array $contentVariables): string
    {
        $body = (string) ($template['body'] ?? '');

        foreach ($contentVariables as $key => $value) {
            $body = str_replace('{{' . $key . '}}', (string) $value, $body);
        }

        return $body;
    }

    /**
     * @return array<string, mixed>
     */
    public function formatStoredMessage(WhatsAppMessage $message): array
    {
        return [
            'sid' => $message->twilio_sid,
            'body' => $message->body ?? '',
            'status' => $message->status,
            'direction' => $message->direction === WhatsAppMessage::DIRECTION_OUTBOUND ? 'outbound-api' : 'inbound',
            'from' => $message->from_address,
            'to' => $message->to_address,
            'date_sent' => optional($message->sent_at)->toIso8601String(),
            'error_message' => $message->metadata['error_message'] ?? null,
            'metadata' => $message->metadata,
        ];
    }
}
