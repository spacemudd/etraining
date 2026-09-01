<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\PersistWhatsAppInboundMedia;
use App\Models\Back\WhatsAppMessage;
use App\Services\WhatsAppInboundMediaPersister;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class BackfillWhatsAppInboundMediaCommand extends Command
{
    protected $signature = 'whatsapp:backfill-media
                            {--id= : Backfill a single message UUID}
                            {--since= : Only messages on/after this datetime (Y-m-d or Y-m-d H:i:s)}
                            {--days= : Only messages from the last N days}
                            {--limit=0 : Max messages to process (0 = no limit)}
                            {--queue : Dispatch Horizon jobs instead of downloading now}
                            {--dry-run : List candidates without downloading}';

    protected $description = 'Copy existing inbound WhatsApp attachments from Telnyx/Twilio URLs onto S3';

    public function handle(WhatsAppInboundMediaPersister $persister): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $useQueue = (bool) $this->option('queue');
        $limit = max(0, (int) $this->option('limit'));

        if ($useQueue && $dryRun) {
            $this->error('Use either --queue or --dry-run, not both.');

            return self::FAILURE;
        }

        if ($useQueue) {
            $this->warn('Queued jobs retry failed downloads up to 5 times. Prefer running without --queue so expired URLs are skipped.');
        }

        $candidates = 0;
        $storedFiles = 0;
        $queued = 0;
        $failed = 0;

        $this->candidateQuery()->chunkById(50, function ($messages) use (
            $persister,
            $dryRun,
            $useQueue,
            $limit,
            &$candidates,
            &$storedFiles,
            &$queued,
            &$failed
        ): bool {
            foreach ($messages as $message) {
                if ($limit > 0 && $candidates >= $limit) {
                    return false;
                }

                if (! $message instanceof WhatsAppMessage || ! $message->inboundMediaNeedsPersist()) {
                    continue;
                }

                $candidates++;
                $itemCount = count($message->inboundMediaItems());

                if ($dryRun) {
                    $this->line(sprintf(
                        '%s  %s  %d attachment(s)  %s',
                        optional($message->sent_at)->toDateTimeString() ?: 'no-date',
                        $message->id,
                        $itemCount,
                        $message->phone
                    ));
                    continue;
                }

                if ($useQueue) {
                    PersistWhatsAppInboundMedia::dispatch($message->id);
                    $queued++;
                    continue;
                }

                try {
                    $saved = $persister->persist($message, false);
                    $storedFiles += $saved;
                    if ($saved > 0) {
                        $this->info(sprintf('Saved %d file(s) for %s', $saved, $message->id));
                    } else {
                        $this->warn(sprintf('No files saved for %s (URL likely expired)', $message->id));
                        $failed++;
                    }
                } catch (Throwable $exception) {
                    $failed++;
                    $this->error(sprintf('%s: %s', $message->id, $exception->getMessage()));
                }
            }

            return true;
        });

        $this->newLine();
        $this->info(sprintf(
            'Candidates: %d. Saved files: %d. Queued: %d. Failed/expired: %d.',
            $candidates,
            $storedFiles,
            $queued,
            $failed
        ));

        if ($candidates === 0) {
            $this->info('Nothing to backfill.');
        }

        return self::SUCCESS;
    }

    private function candidateQuery(): Builder
    {
        $query = WhatsAppMessage::query()
            ->with('media')
            ->where('direction', WhatsAppMessage::DIRECTION_INBOUND)
            ->where('is_note', false)
            ->whereNotNull('metadata');

        $driver = $query->getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $query->whereRaw("JSON_EXTRACT(metadata, '$.media') IS NOT NULL")
                ->whereRaw('JSON_LENGTH(JSON_EXTRACT(metadata, \'$.media\')) > 0');
        }

        $id = trim((string) $this->option('id'));
        if ($id !== '') {
            $query->where('id', $id);
        }

        $since = trim((string) $this->option('since'));
        $days = (int) $this->option('days');
        if ($since !== '') {
            $query->where('sent_at', '>=', $since);
        } elseif ($days > 0) {
            $query->where('sent_at', '>=', now()->subDays($days));
        }

        return $query;
    }
}
