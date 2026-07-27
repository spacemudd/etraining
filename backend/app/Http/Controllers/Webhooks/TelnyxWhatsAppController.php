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

        $media = [];

        foreach ($payload['media'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $media[] = [
                'url' => $item['url'] ?? null,
                'content_type' => $item['content_type'] ?? null,
            ];
        }

        $this->whatsAppService->storeInboundMessage([
            'external_id' => $messageId,
            'from' => $from,
            'to' => $this->extractPhone($payload['to'][0] ?? $payload['to'] ?? null),
            'body' => (string) ($payload['text'] ?? $payload['body'] ?? ''),
            'status' => 'received',
            'sent_at' => $payload['received_at'] ?? now(),
            'metadata' => $media !== [] ? ['media' => $media] : null,
        ]);

        Log::info('Telnyx WhatsApp inbound message stored', [
            'message_id' => $messageId,
            'from' => $from,
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

        $body = (string) data_get($msg, 'text.body', $msg['body'] ?? '');
        $timestamp = $msg['timestamp'] ?? null;
        $sentAt = $timestamp ? \Carbon\Carbon::createFromTimestamp((int) $timestamp) : now();

        $media = [];
        foreach ($msg['media'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }
            $media[] = [
                'url' => $item['url'] ?? null,
                'content_type' => $item['content_type'] ?? null,
            ];
        }

        $metadata = $media !== [] ? ['media' => $media] : null;
        if (isset($fullPayload['contacts'])) {
            $metadata['contacts'] = $fullPayload['contacts'];
        }

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
