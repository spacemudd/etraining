<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Back\WhatsAppConversation;
use Carbon\CarbonInterface;

final class WhatsAppMessagingWindow
{
    public const HOURS = 24;

    /**
     * @return array{
     *     last_inbound_at: string|null,
     *     expires_at: string|null,
     *     remaining_seconds: int,
     *     is_open: bool
     * }
     */
    public static function forConversation(WhatsAppConversation $conversation, ?CarbonInterface $now = null): array
    {
        return self::forLastInbound($conversation->last_inbound_at, $now);
    }

    /**
     * @return array{
     *     last_inbound_at: string|null,
     *     expires_at: string|null,
     *     remaining_seconds: int,
     *     is_open: bool
     * }
     */
    public static function forLastInbound(?CarbonInterface $lastInbound, ?CarbonInterface $now = null): array
    {
        $now = $now ?? now();

        if (! $lastInbound) {
            return [
                'last_inbound_at' => null,
                'expires_at' => null,
                'remaining_seconds' => 0,
                'is_open' => false,
            ];
        }

        $expiresAt = $lastInbound->copy()->addHours(self::HOURS);
        $remaining = max(0, $expiresAt->getTimestamp() - $now->getTimestamp());

        return [
            'last_inbound_at' => $lastInbound->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
            'remaining_seconds' => $remaining,
            'is_open' => $remaining > 0,
        ];
    }
}
