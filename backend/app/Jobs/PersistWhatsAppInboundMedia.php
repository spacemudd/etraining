<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\WhatsAppMessageReceived;
use App\Models\Back\WhatsAppMessage;
use App\Services\WhatsAppInboundMediaPersister;
use App\Support\PusherSocketId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PersistWhatsAppInboundMedia implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    public int $timeout = 120;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60, 120];

    public function __construct(public string $messageId)
    {
    }

    public function uniqueId(): string
    {
        return $this->messageId;
    }

    public function handle(WhatsAppInboundMediaPersister $persister): void
    {
        $message = WhatsAppMessage::query()->find($this->messageId);
        if (! $message) {
            return;
        }

        $stored = $persister->persist($message);
        if ($stored < 1) {
            return;
        }

        $fresh = $message->fresh(['media', 'user']);
        if (! $fresh) {
            return;
        }

        $this->broadcastUpdated($fresh);
    }

    private function broadcastUpdated(WhatsAppMessage $message): void
    {
        $driver = (string) config('broadcasting.default');
        if (! in_array($driver, ['pusher', 'redis', 'log'], true)) {
            return;
        }

        try {
            PusherSocketId::normalizeRequest();
            $pending = broadcast(new WhatsAppMessageReceived($message));
            unset($pending);
        } catch (Throwable $exception) {
            Log::warning('WhatsApp media persist broadcast failed', [
                'message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
