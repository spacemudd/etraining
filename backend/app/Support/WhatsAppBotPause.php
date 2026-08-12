<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Back\WhatsAppBotSession;
use App\Models\Back\WhatsAppConversation;
use App\Services\TelnyxWhatsAppService;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;

final class WhatsAppBotPause
{
    public static function pauseForAgent(string $phone, ?int $minutes = null): void
    {
        $minutes = $minutes ?? (int) config('whatsapp.bot_pause_minutes', 720);
        $service = app(TelnyxWhatsAppService::class);
        $normalizedPhone = $service->normalizePhoneDigits($phone);

        if ($normalizedPhone === '') {
            return;
        }

        $pausedUntil = now()->addMinutes(max(1, $minutes));

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone' => $normalizedPhone],
            ['status' => WhatsAppConversation::STATUS_OPEN]
        );

        $conversation->bot_paused_until = $pausedUntil;
        $conversation->save();

        WhatsAppBotSession::query()
            ->where('phone', $normalizedPhone)
            ->update([
                'restart_pending' => true,
                'current_node_id' => null,
                'context' => null,
            ]);

        Log::info('WhatsApp bot paused for agent handoff', [
            'phone' => $normalizedPhone,
            'paused_until' => $pausedUntil->toIso8601String(),
            'minutes' => $minutes,
        ]);
    }

    public static function pauseIndefinitely(string $phone): void
    {
        $service = app(TelnyxWhatsAppService::class);
        $normalizedPhone = $service->normalizePhoneDigits($phone);

        if ($normalizedPhone === '') {
            return;
        }

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone' => $normalizedPhone],
            ['status' => WhatsAppConversation::STATUS_OPEN]
        );

        // Far-future pause until a human agent replies (which replaces with the normal window).
        $conversation->bot_paused_until = now()->addYears(10);
        $conversation->status = WhatsAppConversation::STATUS_PENDING;
        $conversation->save();

        WhatsAppBotSession::query()
            ->where('phone', $normalizedPhone)
            ->update([
                'restart_pending' => true,
                'current_node_id' => null,
                'context' => null,
            ]);
    }

    public static function isPaused(WhatsAppConversation $conversation, ?CarbonInterface $now = null): bool
    {
        if (! $conversation->bot_paused_until) {
            return false;
        }

        $now = $now ?? now();

        return $conversation->bot_paused_until->greaterThan($now);
    }

    public static function pauseExpired(WhatsAppConversation $conversation, ?CarbonInterface $now = null): bool
    {
        if (! $conversation->bot_paused_until) {
            return false;
        }

        $now = $now ?? now();

        return $conversation->bot_paused_until->isPast();
    }

    public static function resume(string $phone): void
    {
        $service = app(TelnyxWhatsAppService::class);
        $normalizedPhone = $service->normalizePhoneDigits($phone);

        if ($normalizedPhone === '') {
            return;
        }

        $conversation = WhatsAppConversation::query()->where('phone', $normalizedPhone)->first();
        if (! $conversation) {
            $digitsOnly = preg_replace('/\D+/', '', $normalizedPhone) ?? '';
            $conversation = WhatsAppConversation::query()
                ->where(function ($query) use ($normalizedPhone, $digitsOnly) {
                    $query->where('phone', $normalizedPhone);
                    if ($digitsOnly !== '' && $digitsOnly !== $normalizedPhone) {
                        $query->orWhere('phone', $digitsOnly)
                            ->orWhere('phone', '+' . $digitsOnly);
                    }
                })
                ->first();
        }

        if (! $conversation) {
            return;
        }

        $conversation->bot_paused_until = null;
        $conversation->save();

        WhatsAppBotSession::query()
            ->where('phone', $normalizedPhone)
            ->update([
                'restart_pending' => true,
                'current_node_id' => null,
                'context' => null,
            ]);

        Log::info('WhatsApp bot resumed by agent', [
            'phone' => $normalizedPhone,
        ]);
    }
}
