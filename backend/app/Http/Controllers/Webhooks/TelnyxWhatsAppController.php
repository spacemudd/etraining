<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Back\WhatsAppMessage;
use App\Services\TelnyxWebhookValidator;
use App\Services\TelnyxWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelnyxWhatsAppController extends Controller
{
    public function __construct(
        private readonly TelnyxWhatsAppService $whatsAppService,
        private readonly TelnyxWebhookValidator $webhookValidator,
    ) {
    }

    public function handle(Request $request): Response
    {
        if (! $this->webhookValidator->validate($request)) {
            Log::warning('Telnyx WhatsApp webhook rejected: invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response('Forbidden', 403);
        }

        Log::info('Telnyx WhatsApp webhook received', [
            'request' => $request->all(),
        ]);

        $eventType = (string) data_get($request->all(), 'data.event_type', data_get($request->all(), 'event_type', ''));
        $payload = data_get($request->all(), 'data.payload', data_get($request->all(), 'data', []));

        if (! is_array($payload)) {
            $payload = [];
        }

        if ($eventType === 'message.received') {
            $this->handleInbound($payload);
        } elseif ($eventType === 'whatsapp.messages' || isset($payload['messages'])) {
            $messages = $payload['messages'] ?? [];
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    if (is_array($msg)) {
                        $this->handleWhatsappMessagePayload($msg, $payload);
                    }
                }
            }
        } elseif (in_array($eventType, ['message.sent', 'message.finalized', 'message.delivered', 'message.read', 'message.failed'], true)) {
            $this->handleStatus($payload, $eventType);
        }

        return response('', 204);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleInbound(array $payload): void
    {
        $messageId = (string) ($payload['id'] ?? '');
        $from = $this->extractPhone($payload['from'] ?? null);

        if ($messageId === '' || $from === '') {
            return;
        }

        if (WhatsAppMessage::query()->where('twilio_sid', $messageId)->exists()) {
            return;
        }

        [$media, $body] = $this->extractMediaAndBody($payload);

        $metadata = [];
        if ($media !== []) {
            $metadata['media'] = $media;
        }
        $buttonMeta = $this->extractButtonMetadata($payload);
        if ($buttonMeta !== []) {
            $metadata['button'] = $buttonMeta;
        }
        $metadata = $metadata !== [] ? $metadata : null;

        $this->whatsAppService->storeInboundMessage([
            'external_id' => $messageId,
            'from' => $from,
            'to' => $this->extractPhone($payload['to'][0] ?? $payload['to'] ?? null),
            'body' => $body,
            'status' => 'received',
            'sent_at' => $payload['received_at'] ?? now(),
            'metadata' => $metadata,
        ]);

        Log::info('Telnyx WhatsApp inbound message stored', [
            'message_id' => $messageId,
            'from' => $from,
            'body' => $body,
        ]);
    }

    /**
     * @param  array<string, mixed>  $msg
     * @param  array<string, mixed>  $fullPayload
     */
    private function handleWhatsappMessagePayload(array $msg, array $fullPayload): void
    {
        $messageId = (string) ($msg['id'] ?? '');
        $from = (string) ($msg['from'] ?? '');
        if ($from === '' && isset($fullPayload['contacts'][0]['wa_id'])) {
            $from = '+' . ltrim((string) $fullPayload['contacts'][0]['wa_id'], '+');
        }

        if ($messageId === '' || $from === '') {
            return;
        }

        if (WhatsAppMessage::query()->where('twilio_sid', $messageId)->exists()) {
            return;
        }

        [$media, $body] = $this->extractMediaAndBody($msg);
        if ($body === '' && isset($fullPayload['text'])) {
            $body = $this->stringifyWebhookText($fullPayload['text']);
        }

        $timestamp = $msg['timestamp'] ?? null;
        $sentAt = $timestamp ? \Carbon\Carbon::createFromTimestamp((int) $timestamp) : now();

        $metadata = [];
        if ($media !== []) {
            $metadata['media'] = $media;
        }
        if (isset($fullPayload['contacts'])) {
            $metadata['contacts'] = $fullPayload['contacts'];
        }
        $buttonMeta = $this->extractButtonMetadata($msg);
        if ($buttonMeta === []) {
            $buttonMeta = $this->extractButtonMetadata($fullPayload);
        }
        if ($buttonMeta !== []) {
            $metadata['button'] = $buttonMeta;
        }
        $metadata = $metadata !== [] ? $metadata : null;

        $this->whatsAppService->storeInboundMessage([
            'external_id' => $messageId,
            'from' => $from,
            'to' => data_get($fullPayload, 'metadata.display_phone_number'),
            'body' => $body,
            'status' => 'received',
            'sent_at' => $sentAt,
            'metadata' => $metadata,
        ]);

        Log::info('Telnyx WhatsApp incoming message (whatsapp.messages) stored', [
            'message_id' => $messageId,
            'from' => $from,
            'body' => $body,
        ]);
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: string}
     */
    private function extractMediaAndBody(array $source): array
    {
        $media = [];
        $body = $this->extractMessageBody($source);

        foreach ($source['media'] ?? [] as $item) {
            if (is_array($item) && isset($item['url'])) {
                $media[] = [
                    'url' => $item['url'],
                    'content_type' => $item['content_type'] ?? $item['mime_type'] ?? null,
                ];
            }
        }

        foreach (['image', 'document', 'audio', 'video'] as $mediaType) {
            if (isset($source[$mediaType]) && is_array($source[$mediaType])) {
                $m = $source[$mediaType];
                if (isset($m['url'])) {
                    $media[] = [
                        'url' => $m['url'],
                        'content_type' => $m['content_type'] ?? $m['mime_type'] ?? null,
                    ];
                }
            }
        }

        if ($body === '' && count($media) > 0) {
            $body = '[Media Attachment]';
        }

        return [$media, $body];
    }

    /**
     * WhatsApp / Telnyx payloads may send text as a string or as { body: "..." }.
     * Button / interactive replies arrive without a text body — use the button title.
     * Casting an array with (string) raises "Array to string conversion" (E-TRAINING-20H).
     *
     * @param  array<string, mixed>  $source
     */
    private function extractMessageBody(array $source): string
    {
        $candidates = [
            data_get($source, 'text.body'),
            $source['text'] ?? null,
            $source['body'] ?? null,
            data_get($source, 'image.caption'),
            data_get($source, 'document.caption'),
            data_get($source, 'video.caption'),
            data_get($source, 'caption'),
            // Quick-reply / template button taps
            data_get($source, 'button.text'),
            data_get($source, 'button.payload'),
            data_get($source, 'button_reply.title'),
            data_get($source, 'button_reply.id'),
            data_get($source, 'interactive.button_reply.title'),
            data_get($source, 'interactive.button_reply.id'),
            data_get($source, 'interactive.list_reply.title'),
            data_get($source, 'interactive.list_reply.id'),
            data_get($source, 'interactive.list_reply.description'),
            // Nested whatsapp_message shapes from some Telnyx events
            data_get($source, 'whatsapp_message.button.text'),
            data_get($source, 'whatsapp_message.button.payload'),
            data_get($source, 'whatsapp_message.interactive.button_reply.title'),
            data_get($source, 'whatsapp_message.interactive.list_reply.title'),
            data_get($source, 'whatsapp_message.text.body'),
        ];

        foreach ($candidates as $candidate) {
            $text = $this->stringifyWebhookText($candidate);
            if ($text !== '') {
                return $text;
            }
        }

        $type = strtolower((string) ($source['type'] ?? data_get($source, 'whatsapp_message.type', '')));
        if (in_array($type, ['button', 'interactive'], true)) {
            return '[Button reply]';
        }

        return '';
    }

    /**
     * @param  mixed  $value
     */
    private function stringifyWebhookText($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (! is_array($value)) {
            return '';
        }

        foreach (['body', 'text', 'caption'] as $key) {
            if (isset($value[$key]) && is_scalar($value[$key])) {
                return (string) $value[$key];
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleStatus(array $payload, string $eventType): void
    {
        $messageId = (string) ($payload['id'] ?? '');

        if ($messageId === '') {
            return;
        }

        $status = (string) data_get($payload, 'to.0.status', '');

        if ($status === '') {
            $status = match ($eventType) {
                'message.sent' => 'sent',
                'message.delivered' => 'delivered',
                'message.read' => 'read',
                'message.failed' => 'failed',
                'message.finalized' => (string) data_get($payload, 'to.0.status', 'finalized'),
                default => $eventType,
            };
        }

        $errorMessage = (string) data_get($payload, 'errors.0.detail', data_get($payload, 'errors.0.title', ''));

        $this->whatsAppService->updateMessageStatus(
            $messageId,
            $status,
            $errorMessage !== '' ? $errorMessage : null
        );
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array<string, mixed>
     */
    private function extractButtonMetadata(array $source): array
    {
        $paths = [
            'button',
            'button_reply',
            'interactive.button_reply',
            'interactive.list_reply',
            'whatsapp_message.button',
            'whatsapp_message.interactive.button_reply',
            'whatsapp_message.interactive.list_reply',
        ];

        foreach ($paths as $path) {
            $value = data_get($source, $path);
            if (is_array($value) && $value !== []) {
                return $value;
            }
        }

        return [];
    }

    /**
     * @param  mixed  $value
     */
    private function extractPhone($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            return (string) ($value['phone_number'] ?? $value['phone'] ?? '');
        }

        return '';
    }
}
