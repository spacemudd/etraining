<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\WhatsAppMessagingWindow;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WhatsAppMessagingWindowTest extends TestCase
{
    public function test_closed_when_no_inbound(): void
    {
        $window = WhatsAppMessagingWindow::forLastInbound(null, Carbon::parse('2026-08-09 12:00:00'));

        $this->assertFalse($window['is_open']);
        $this->assertSame(0, $window['remaining_seconds']);
        $this->assertNull($window['expires_at']);
    }

    public function test_open_within_twenty_four_hours(): void
    {
        $now = Carbon::parse('2026-08-09 12:00:00');
        $window = WhatsAppMessagingWindow::forLastInbound($now->copy()->subHours(6), $now);

        $this->assertTrue($window['is_open']);
        $this->assertSame(18 * 3600, $window['remaining_seconds']);
        $this->assertSame($now->copy()->addHours(18)->toIso8601String(), $window['expires_at']);
    }

    public function test_locked_after_twenty_four_hours(): void
    {
        $now = Carbon::parse('2026-08-09 12:00:00');
        $window = WhatsAppMessagingWindow::forLastInbound($now->copy()->subHours(25), $now);

        $this->assertFalse($window['is_open']);
        $this->assertSame(0, $window['remaining_seconds']);
    }
}
