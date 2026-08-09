<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Back\WhatsAppBotSender;
use App\Models\Back\WhatsAppConversation;
use App\Services\TelnyxWhatsAppService;

final class WhatsAppBotStatus
{
    /**
     * @return array{
     *     workflow_assigned: bool,
     *     workflow_name: string|null,
     *     ai_enabled: bool,
     *     is_paused: bool,
     *     is_active: bool,
     *     paused_until: string|null,
     *     pause_minutes: int,
     *     can_pause: bool,
     *     can_resume: bool
     * }
     */
    public static function forPhone(string $phone): array
    {
        $service = app(TelnyxWhatsAppService::class);
        $normalizedPhone = $service->normalizePhoneDigits($phone);
        $configuredFrom = $service->normalizePhoneDigits(
            (string) (config('telnyx.whatsapp_from') ?: config('twilio.whatsapp_from') ?: '')
        );

        $sender = self::findSender($configuredFrom);

        // Fallback: any sender that currently has an active workflow assigned.
        if (! $sender || ! $sender->workflow_id) {
            $sender = WhatsAppBotSender::query()
                ->with('workflow:id,name,is_active')
                ->whereNotNull('workflow_id')
                ->whereHas('workflow', fn ($query) => $query->where('is_active', true))
                ->orderBy('created_at')
                ->first();
        }

        $workflow = $sender?->workflow;
        $workflowAssigned = $workflow !== null && (bool) $workflow->is_active;
        $aiEnabled = WhatsAppAiSettings::isReady();
        $botConfigured = $aiEnabled || $workflowAssigned;

        $conversation = self::findConversation($normalizedPhone);
        $isPaused = $conversation ? WhatsAppBotPause::isPaused($conversation) : false;

        return [
            'workflow_assigned' => $workflowAssigned,
            'workflow_name' => $workflowAssigned ? $workflow->name : null,
            'ai_enabled' => $aiEnabled,
            'is_paused' => $isPaused,
            'is_active' => $botConfigured && ! $isPaused,
            'paused_until' => $isPaused
                ? optional($conversation?->bot_paused_until)->toIso8601String()
                : null,
            'pause_minutes' => (int) config('whatsapp.bot_pause_minutes', 30),
            'can_pause' => $botConfigured && ! $isPaused,
            'can_resume' => $botConfigured && $isPaused,
        ];
    }

    private static function findSender(string $configuredFrom): ?WhatsAppBotSender
    {
        if ($configuredFrom === '') {
            return WhatsAppBotSender::query()
                ->with('workflow:id,name,is_active')
                ->orderBy('created_at')
                ->first();
        }

        $digitsOnly = preg_replace('/\D+/', '', $configuredFrom) ?? '';

        return WhatsAppBotSender::query()
            ->with('workflow:id,name,is_active')
            ->where(function ($query) use ($configuredFrom, $digitsOnly) {
                $query->where('phone', $configuredFrom);
                if ($digitsOnly !== '' && $digitsOnly !== $configuredFrom) {
                    $query->orWhere('phone', $digitsOnly)
                        ->orWhere('phone', '+' . $digitsOnly);
                }
            })
            ->first();
    }

    private static function findConversation(string $normalizedPhone): ?WhatsAppConversation
    {
        if ($normalizedPhone === '') {
            return null;
        }

        $digitsOnly = preg_replace('/\D+/', '', $normalizedPhone) ?? '';

        return WhatsAppConversation::query()
            ->where(function ($query) use ($normalizedPhone, $digitsOnly) {
                $query->where('phone', $normalizedPhone);
                if ($digitsOnly !== '' && $digitsOnly !== $normalizedPhone) {
                    $query->orWhere('phone', $digitsOnly)
                        ->orWhere('phone', '+' . $digitsOnly);
                }
            })
            ->first();
    }
}
