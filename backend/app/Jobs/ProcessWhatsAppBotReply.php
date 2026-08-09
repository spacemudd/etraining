<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Back\WhatsAppMessage;
use App\Services\WhatsAppAiBotService;
use App\Services\WhatsAppBotEngine;
use App\Support\WhatsAppAiSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWhatsAppBotReply implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $messageId)
    {
    }

    public function handle(WhatsAppBotEngine $engine, WhatsAppAiBotService $aiBot): void
    {
        $message = WhatsAppMessage::query()->find($this->messageId);
        if (! $message) {
            return;
        }

        if (WhatsAppAiSettings::isReady()) {
            $aiBot->handleInbound($message);

            return;
        }

        $engine->handleInbound($message);
    }
}
