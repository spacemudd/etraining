<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppBotSender;
use App\Models\Back\WhatsAppBotSession;
use App\Models\Back\WhatsAppBotWorkflow;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Support\WhatsAppBotPause;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppBotEngine
{
    private const MAX_STEPS = 25;

    public function __construct(private readonly TelnyxWhatsAppService $whatsAppService)
    {
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

        $phone = $this->whatsAppService->normalizePhoneDigits((string) $message->phone);
        if ($phone === '') {
            return;
        }

        $senderPhone = $this->resolveSenderPhone($message);
        if ($senderPhone === '') {
            return;
        }

        $sender = WhatsAppBotSender::query()
            ->where('phone', $senderPhone)
            ->with('workflow')
            ->first();

        if (! $sender || ! $sender->workflow_id) {
            return;
        }

        /** @var WhatsAppBotWorkflow|null $workflow */
        $workflow = $sender->workflow;
        if (! $workflow || ! $workflow->is_active) {
            return;
        }

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone' => $phone],
            ['status' => WhatsAppConversation::STATUS_OPEN]
        );

        if (WhatsAppBotPause::isPaused($conversation)) {
            Log::info('WhatsApp bot skipped: conversation paused', [
                'phone' => $phone,
                'paused_until' => optional($conversation->bot_paused_until)->toIso8601String(),
            ]);

            return;
        }

        $session = WhatsAppBotSession::query()->firstOrNew([
            'phone' => $phone,
            'sender_phone' => $senderPhone,
        ]);

        $shouldRestart = $session->restart_pending
            || WhatsAppBotPause::pauseExpired($conversation)
            || ! $session->exists
            || (string) $session->workflow_id !== (string) $workflow->id
            || empty($session->current_node_id);

        if (WhatsAppBotPause::pauseExpired($conversation)) {
            $conversation->bot_paused_until = null;
            $conversation->save();
        }

        if ($shouldRestart) {
            $session->workflow_id = $workflow->id;
            $session->current_node_id = $this->findStartNodeId($workflow);
            $session->context = [];
            $session->restart_pending = false;
            $session->save();
        }

        if (empty($session->current_node_id)) {
            Log::warning('WhatsApp bot: workflow has no start node', [
                'workflow_id' => $workflow->id,
            ]);

            return;
        }

        try {
            $this->advance($workflow, $session, $message, $conversation);
        } catch (Throwable $exception) {
            Log::error('WhatsApp bot engine failed', [
                'phone' => $phone,
                'workflow_id' => $workflow->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function advance(
        WhatsAppBotWorkflow $workflow,
        WhatsAppBotSession $session,
        WhatsAppMessage $inbound,
        WhatsAppConversation $conversation
    ): void {
        $graph = $workflow->normalizedGraph();
        $nodes = $graph['nodes'];
        $connections = $graph['connections'];
        $inboundText = trim((string) ($inbound->body ?? ''));
        $waitingForInput = ! empty(($session->context ?? [])['waiting']);

        $steps = 0;
        while ($steps < self::MAX_STEPS) {
            $steps++;
            $nodeId = (string) $session->current_node_id;
            $node = $nodes[$nodeId] ?? null;

            if (! is_array($node)) {
                Log::warning('WhatsApp bot: missing node', ['node_id' => $nodeId]);
                break;
            }

            $type = (string) ($node['type'] ?? '');
            $data = is_array($node['data'] ?? null) ? $node['data'] : [];

            if ($waitingForInput) {
                $nextId = $this->resolveWaitOrButtonNext(
                    $type,
                    $data,
                    $connections[$nodeId] ?? [],
                    $inboundText,
                    $inbound
                );

                $context = $session->context ?? [];
                unset($context['waiting'], $context['wait_type']);
                $context['last_input'] = $inboundText;
                $session->context = $context;
                $waitingForInput = false;

                if ($nextId === null) {
                    // No matching branch — stay on wait node for another reply.
                    $session->context = array_merge($session->context ?? [], [
                        'waiting' => true,
                        'wait_type' => $type,
                    ]);
                    $session->save();
                    break;
                }

                $session->current_node_id = $nextId;
                $session->save();
                continue;
            }

            if ($type === 'start') {
                $nextId = $this->firstConnectionTarget($connections[$nodeId] ?? []);
                if ($nextId === null) {
                    break;
                }
                $session->current_node_id = $nextId;
                $session->save();
                continue;
            }

            if ($type === 'send_message') {
                $body = $this->interpolate((string) ($data['body'] ?? ''), $inbound, $session);
                if ($body !== '') {
                    $this->sendBotText($inbound->phone, $body, $inbound->trainee_id);
                }
                $nextId = $this->firstConnectionTarget($connections[$nodeId] ?? []);
                if ($nextId === null) {
                    $this->endSession($session);
                    break;
                }
                $session->current_node_id = $nextId;
                $session->save();
                continue;
            }

            if ($type === 'buttons') {
                $body = $this->interpolate((string) ($data['body'] ?? ''), $inbound, $session);
                $buttons = is_array($data['buttons'] ?? null) ? $data['buttons'] : [];
                $lines = [$body];
                foreach (array_values($buttons) as $index => $button) {
                    $label = is_array($button)
                        ? (string) ($button['label'] ?? $button['text'] ?? '')
                        : (string) $button;
                    if ($label !== '') {
                        $lines[] = ($index + 1) . '. ' . $label;
                    }
                }
                $text = trim(implode("\n", array_filter($lines)));
                if ($text !== '') {
                    $this->sendBotText($inbound->phone, $text, $inbound->trainee_id);
                }
                $session->context = array_merge($session->context ?? [], [
                    'waiting' => true,
                    'wait_type' => 'buttons',
                ]);
                $session->save();
                break;
            }

            if ($type === 'wait_input') {
                $prompt = $this->interpolate((string) ($data['prompt'] ?? $data['body'] ?? ''), $inbound, $session);
                if ($prompt !== '') {
                    $this->sendBotText($inbound->phone, $prompt, $inbound->trainee_id);
                }
                $session->context = array_merge($session->context ?? [], [
                    'waiting' => true,
                    'wait_type' => 'wait_input',
                ]);
                $session->save();
                break;
            }

            if ($type === 'condition') {
                $nextId = $this->resolveConditionNext(
                    $data,
                    $connections[$nodeId] ?? [],
                    (string) (($session->context ?? [])['last_input'] ?? $inboundText)
                );
                if ($nextId === null) {
                    $nextId = $this->firstConnectionTarget($connections[$nodeId] ?? []);
                }
                if ($nextId === null) {
                    $this->endSession($session);
                    break;
                }
                $session->current_node_id = $nextId;
                $session->save();
                continue;
            }

            if ($type === 'assign_agent') {
                $notice = $this->interpolate((string) ($data['body'] ?? ''), $inbound, $session);
                if ($notice !== '') {
                    $this->sendBotText($inbound->phone, $notice, $inbound->trainee_id);
                }
                WhatsAppBotPause::pauseIndefinitely($inbound->phone);
                $session->refresh();
                break;
            }

            if ($type === 'end') {
                $body = $this->interpolate((string) ($data['body'] ?? ''), $inbound, $session);
                if ($body !== '') {
                    $this->sendBotText($inbound->phone, $body, $inbound->trainee_id);
                }
                $this->endSession($session);
                break;
            }

            Log::warning('WhatsApp bot: unknown node type', [
                'type' => $type,
                'node_id' => $nodeId,
            ]);
            break;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $connections
     */
    private function resolveWaitOrButtonNext(
        string $type,
        array $data,
        array $connections,
        string $inboundText,
        WhatsAppMessage $inbound
    ): ?string {
        $normalizedInput = mb_strtolower(trim($inboundText));
        $buttonPayload = '';
        $metadata = is_array($inbound->metadata) ? $inbound->metadata : [];
        if (! empty($metadata['button_payload'])) {
            $buttonPayload = mb_strtolower(trim((string) $metadata['button_payload']));
        } elseif (! empty($metadata['button_text'])) {
            $buttonPayload = mb_strtolower(trim((string) $metadata['button_text']));
        }

        if ($type === 'buttons') {
            $buttons = is_array($data['buttons'] ?? null) ? $data['buttons'] : [];
            foreach (array_values($buttons) as $index => $button) {
                $label = is_array($button)
                    ? (string) ($button['label'] ?? $button['text'] ?? '')
                    : (string) $button;
                $value = is_array($button)
                    ? (string) ($button['value'] ?? $label)
                    : $label;
                $labelNorm = mb_strtolower(trim($label));
                $valueNorm = mb_strtolower(trim($value));
                $number = (string) ($index + 1);

                $matched = $normalizedInput === $labelNorm
                    || $normalizedInput === $valueNorm
                    || $normalizedInput === $number
                    || ($buttonPayload !== '' && ($buttonPayload === $labelNorm || $buttonPayload === $valueNorm));

                if (! $matched) {
                    continue;
                }

                $output = is_array($button) ? (string) ($button['output'] ?? ('output_' . ($index + 1))) : ('output_' . ($index + 1));

                return $this->connectionTargetByOutput($connections, $output)
                    ?? $this->connectionTargetByKeyword($connections, $labelNorm)
                    ?? $this->connectionTargetByKeyword($connections, $valueNorm)
                    ?? $this->firstConnectionTarget($connections);
            }

            return null;
        }

        // wait_input
        $keywords = is_array($data['keywords'] ?? null) ? $data['keywords'] : [];
        foreach ($keywords as $index => $keywordRule) {
            $keyword = is_array($keywordRule)
                ? (string) ($keywordRule['keyword'] ?? $keywordRule['text'] ?? '')
                : (string) $keywordRule;
            $keywordNorm = mb_strtolower(trim($keyword));
            if ($keywordNorm === '' || $normalizedInput === '') {
                continue;
            }

            $mode = is_array($keywordRule) ? (string) ($keywordRule['mode'] ?? 'contains') : 'contains';
            $matched = $mode === 'equals'
                ? $normalizedInput === $keywordNorm
                : str_contains($normalizedInput, $keywordNorm);

            if (! $matched) {
                continue;
            }

            $output = is_array($keywordRule)
                ? (string) ($keywordRule['output'] ?? ('output_' . ($index + 1)))
                : ('output_' . ($index + 1));

            return $this->connectionTargetByOutput($connections, $output)
                ?? $this->connectionTargetByKeyword($connections, $keywordNorm)
                ?? $this->firstConnectionTarget($connections);
        }

        $match = (string) ($data['match'] ?? 'any');
        if ($match === 'any' || $keywords === []) {
            return $this->connectionTargetByOutput($connections, 'output_1')
                ?? $this->firstConnectionTarget($connections);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $connections
     */
    private function resolveConditionNext(array $data, array $connections, string $text): ?string
    {
        $normalizedInput = mb_strtolower(trim($text));
        $keyword = mb_strtolower(trim((string) ($data['keyword'] ?? '')));
        $mode = (string) ($data['mode'] ?? 'contains');

        $matched = false;
        if ($keyword !== '' && $normalizedInput !== '') {
            $matched = $mode === 'equals'
                ? $normalizedInput === $keyword
                : str_contains($normalizedInput, $keyword);
        }

        if ($matched) {
            return $this->connectionTargetByOutput($connections, 'output_true')
                ?? $this->connectionTargetByOutput($connections, 'output_1')
                ?? $this->firstConnectionTarget($connections);
        }

        return $this->connectionTargetByOutput($connections, 'output_false')
            ?? $this->connectionTargetByOutput($connections, 'output_2')
            ?? null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $connections
     */
    private function firstConnectionTarget(array $connections): ?string
    {
        foreach ($connections as $connection) {
            $to = (string) ($connection['to_node'] ?? $connection['to'] ?? '');
            if ($to !== '') {
                return $to;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $connections
     */
    private function connectionTargetByOutput(array $connections, string $output): ?string
    {
        foreach ($connections as $connection) {
            $fromOutput = (string) ($connection['from_output'] ?? $connection['output'] ?? '');
            if ($fromOutput === $output) {
                $to = (string) ($connection['to_node'] ?? $connection['to'] ?? '');

                return $to !== '' ? $to : null;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $connections
     */
    private function connectionTargetByKeyword(array $connections, string $keyword): ?string
    {
        foreach ($connections as $connection) {
            $connKeyword = mb_strtolower(trim((string) ($connection['keyword'] ?? '')));
            if ($connKeyword !== '' && $connKeyword === $keyword) {
                $to = (string) ($connection['to_node'] ?? $connection['to'] ?? '');

                return $to !== '' ? $to : null;
            }
        }

        return null;
    }

    private function findStartNodeId(WhatsAppBotWorkflow $workflow): ?string
    {
        $nodes = $workflow->normalizedGraph()['nodes'];

        foreach ($nodes as $id => $node) {
            if (($node['type'] ?? '') === 'start') {
                return (string) $id;
            }
        }

        $firstKey = array_key_first($nodes);

        return $firstKey !== null ? (string) $firstKey : null;
    }

    private function endSession(WhatsAppBotSession $session): void
    {
        $session->current_node_id = null;
        $session->context = [];
        $session->restart_pending = true;
        $session->save();
    }

    private function sendBotText(string $phone, string $body, ?string $traineeId): void
    {
        if (! $this->whatsAppService->isConfigured()) {
            Log::warning('WhatsApp bot: Telnyx not configured, storing outbound locally only');
            $this->storeLocalBotMessage($phone, $body, $traineeId);

            return;
        }

        try {
            $this->whatsAppService->sendFreeformMessage($phone, $body, $traineeId);
        } catch (Throwable $exception) {
            // Fall back to local store so flows remain testable without Telnyx.
            Log::warning('WhatsApp bot send failed, storing locally', [
                'error' => $exception->getMessage(),
            ]);
            $this->storeLocalBotMessage($phone, $body, $traineeId);

            return;
        }

        $normalized = $this->whatsAppService->normalizePhoneDigits($phone);
        $stored = WhatsAppMessage::query()
            ->where('phone', $normalized)
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->where('body', $body)
            ->orderByDesc('created_at')
            ->first();

        if ($stored) {
            $metadata = is_array($stored->metadata) ? $stored->metadata : [];
            $metadata['is_bot'] = true;
            $stored->metadata = $metadata;
            $stored->user_id = null;
            $stored->save();
        }
    }
    private function storeLocalBotMessage(string $phone, string $body, ?string $traineeId): void
    {
        $normalized = $this->whatsAppService->normalizePhoneDigits($phone);
        $message = WhatsAppMessage::query()->create([
            'trainee_id' => $traineeId,
            'phone' => $normalized,
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'body' => $body,
            'status' => 'sent',
            'from_address' => config('telnyx.whatsapp_from'),
            'to_address' => $normalized,
            'sent_at' => now(),
            'metadata' => ['is_bot' => true],
        ]);

        \App\Support\WhatsAppBroadcast::messageStored($message);
    }

    private function interpolate(string $body, WhatsAppMessage $inbound, WhatsAppBotSession $session): string
    {
        if ($body === '') {
            return '';
        }

        $trainee = null;
        try {
            $trainee = $inbound->trainee_id
                ? Trainee::query()->with('company')->find($inbound->trainee_id)
                : $this->whatsAppService->findTraineeByPhone(
                    $this->whatsAppService->normalizePhoneDigits((string) $inbound->phone)
                );
        } catch (Throwable $exception) {
            Log::debug('WhatsApp bot trainee lookup skipped', [
                'error' => $exception->getMessage(),
            ]);
        }

        $replacements = [
            '{{trainee_name}}' => $trainee?->name ?? '',
            '{{trainee_english_name}}' => $trainee?->english_name ?? $trainee?->name ?? '',
            '{{trainee_phone}}' => $trainee?->phone ?? $inbound->phone ?? '',
            '{{trainee_identity}}' => $trainee?->identity_number ?? '',
            '{{company_name}}' => $trainee?->company?->name_ar ?? '',
            '{{last_input}}' => (string) (($session->context ?? [])['last_input'] ?? ''),
        ];

        return strtr($body, $replacements);
    }

    private function resolveSenderPhone(WhatsAppMessage $message): string
    {
        $to = (string) ($message->to_address ?? '');
        $digits = $this->whatsAppService->normalizePhoneDigits($to);
        if ($digits !== '') {
            return $digits;
        }

        return $this->whatsAppService->normalizePhoneDigits((string) config('telnyx.whatsapp_from'));
    }
}
