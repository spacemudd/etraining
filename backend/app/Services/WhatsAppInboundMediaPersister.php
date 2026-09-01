<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppMessage;
use App\Models\Media;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class WhatsAppInboundMediaPersister
{
    /**
     * @return int Number of newly stored files
     */
    public function persist(WhatsAppMessage $message, bool $failOnItemError = true): int
    {
        $items = $message->inboundMediaItems();
        if ($items === []) {
            return 0;
        }

        $message->loadMissing('media');
        $stored = 0;

        foreach ($items as $item) {
            $source = $this->sourceKey($item);
            if ($source !== '' && $this->alreadyStored($message, $source)) {
                continue;
            }

            try {
                $downloaded = $this->download($message, $item);
                if ($downloaded === null) {
                    continue;
                }

                $this->storeOnS3($message, $downloaded, $source);
                $stored++;
            } catch (Throwable $exception) {
                Log::error('WhatsApp inbound media persist failed for item', [
                    'message_id' => $message->id,
                    'source' => $source,
                    'error' => $exception->getMessage(),
                ]);

                if ($failOnItemError) {
                    throw $exception;
                }
            }
        }

        if ($stored > 0) {
            $message->unsetRelation('media');
        }

        return $stored;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public function sourceKey(array $item): string
    {
        $url = trim((string) ($item['url'] ?? ''));
        if ($url !== '') {
            return $url;
        }

        return trim((string) ($item['id'] ?? ''));
    }

    private function alreadyStored(WhatsAppMessage $message, string $source): bool
    {
        return $message->getMedia('whatsapp_media')->contains(
            static fn (Media $media): bool => (string) $media->getCustomProperty('source') === $source
        );
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{contents: string, mime: string, filename: string}|null
     */
    public function download(WhatsAppMessage $message, array $item): ?array
    {
        $url = trim((string) ($item['url'] ?? ''));
        $hintMime = $this->itemMime($item);

        if ($url !== '') {
            return $this->downloadFromUrl($url, $hintMime);
        }

        $mediaId = trim((string) ($item['id'] ?? ''));
        if ($mediaId === '') {
            return null;
        }

        return $this->downloadTelnyxWhatsAppMedia($message, $mediaId, $hintMime);
    }

    /**
     * @return array{contents: string, mime: string, filename: string}
     */
    private function downloadFromUrl(string $url, ?string $hintMime): array
    {
        $request = Http::timeout(60)->withHeaders([
            'Accept' => '*/*',
        ]);

        if ($this->isTwilioUrl($url)) {
            $request = $request->withBasicAuth(
                (string) config('twilio.account_sid'),
                (string) config('twilio.auth_token')
            );
        } elseif ($this->isTelnyxUrl($url) && filled(config('telnyx.api_key'))) {
            $request = $request->withToken((string) config('telnyx.api_key'));
        }

        $response = $request->get($url);

        if ($response->unauthorized() && ! $this->isTwilioUrl($url) && filled(config('telnyx.api_key'))) {
            $response = Http::timeout(60)
                ->withToken((string) config('telnyx.api_key'))
                ->withHeaders(['Accept' => '*/*'])
                ->get($url);
        }

        $this->assertSuccessfulDownload($response, $url);

        return $this->normalizeDownload($response, $hintMime, $url);
    }

    /**
     * @return array{contents: string, mime: string, filename: string}
     */
    private function downloadTelnyxWhatsAppMedia(WhatsAppMessage $message, string $mediaId, ?string $hintMime): array
    {
        $apiKey = (string) config('telnyx.api_key');
        if ($apiKey === '') {
            throw new RuntimeException('TELNYX_API_KEY is not configured.');
        }

        $phone = $this->businessPhone($message);
        $url = sprintf(
            'https://api.telnyx.com/v2/whatsapp/media/%s/%s',
            rawurlencode($phone),
            rawurlencode($mediaId)
        );

        $response = Http::timeout(60)
            ->withToken($apiKey)
            ->withHeaders(['Accept' => '*/*'])
            ->get($url);

        $this->assertSuccessfulDownload($response, $url);

        return $this->normalizeDownload($response, $hintMime, $mediaId);
    }

    private function businessPhone(WhatsAppMessage $message): string
    {
        $candidates = [
            (string) $message->to_address,
            (string) config('telnyx.whatsapp_from'),
        ];

        foreach ($candidates as $candidate) {
            $digits = preg_replace('/\D+/', '', $candidate) ?? '';
            if ($digits !== '') {
                return '+' . ltrim($digits, '0');
            }
        }

        throw new RuntimeException('Cannot resolve WhatsApp business number for media download.');
    }

    /**
     * @return array{contents: string, mime: string, filename: string}
     */
    private function normalizeDownload(Response $response, ?string $hintMime, string $source): array
    {
        $contents = $response->body();
        if ($contents === '') {
            throw new RuntimeException('Downloaded WhatsApp media was empty.');
        }

        $mime = $hintMime
            ?: $this->headerMime($response)
            ?: 'application/octet-stream';

        return [
            'contents' => $contents,
            'mime' => $mime,
            'filename' => $this->filenameFor($response, $mime, $source),
        ];
    }

    private function assertSuccessfulDownload(Response $response, string $url): void
    {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException(
            'WhatsApp media download failed HTTP ' . $response->status() . ' for ' . $url
        );
    }

    /**
     * @param  array{contents: string, mime: string, filename: string}  $downloaded
     */
    private function storeOnS3(WhatsAppMessage $message, array $downloaded, string $source): void
    {
        $teamId = $this->resolveTeamId($message);
        $tmp = tempnam(sys_get_temp_dir(), 'wa-media-');
        if ($tmp === false) {
            throw new RuntimeException('Could not create a temp file for WhatsApp media.');
        }

        try {
            file_put_contents($tmp, $downloaded['contents']);

            $fileAdder = $message->addMedia($tmp)
                ->usingFileName($downloaded['filename'])
                ->usingName(pathinfo($downloaded['filename'], PATHINFO_FILENAME))
                ->withCustomProperties(['source' => $source]);

            $media = $fileAdder->toMediaCollection('whatsapp_media', 's3');

            $dirty = false;
            if ($teamId && $media->team_id !== $teamId) {
                $media->team_id = $teamId;
                $dirty = true;
            }
            if ($downloaded['mime'] !== '' && $media->mime_type !== $downloaded['mime']) {
                $media->mime_type = $downloaded['mime'];
                $dirty = true;
            }
            if ($dirty) {
                $media->save();
            }
        } finally {
            if (is_file($tmp)) {
                @unlink($tmp);
            }
        }
    }

    private function resolveTeamId(WhatsAppMessage $message): ?string
    {
        if ($message->trainee_id) {
            $trainee = Trainee::query()->withoutGlobalScopes()->find($message->trainee_id);
            if ($trainee && filled($trainee->team_id)) {
                return (string) $trainee->team_id;
            }
        }

        $teamId = \Illuminate\Support\Facades\DB::table('teams')->value('id');

        return $teamId ? (string) $teamId : null;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function itemMime(array $item): ?string
    {
        foreach (['content_type', 'mime_type'] as $key) {
            $value = $item[$key] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function headerMime(Response $response): ?string
    {
        $header = $response->header('Content-Type');
        if (! is_string($header) || $header === '') {
            return null;
        }

        return trim(explode(';', $header)[0]);
    }

    private function filenameFor(Response $response, string $mime, string $source): string
    {
        $disposition = (string) $response->header('Content-Disposition');
        if (preg_match('/filename\*?=(?:UTF-8\'\')?"?([^";]+)"?/i', $disposition, $matches) === 1) {
            $name = basename(urldecode(trim($matches[1])));
            if ($name !== '') {
                return $name;
            }
        }

        $ext = $this->extensionFromMime($mime);
        $path = parse_url($source, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            $base = basename($path);
            if ($base !== '' && str_contains($base, '.')) {
                return $base;
            }
        }

        return 'whatsapp-' . substr(sha1($source), 0, 12) . '.' . $ext;
    }

    private function extensionFromMime(string $mime): string
    {
        $mime = strtolower(trim(explode(';', $mime)[0]));

        return match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'audio/ogg', 'audio/opus' => 'ogg',
            'audio/mpeg' => 'mp3',
            'audio/aac' => 'aac',
            'video/mp4' => 'mp4',
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            default => Str::after($mime, '/') ?: 'bin',
        };
    }

    private function isTwilioUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return str_ends_with($host, 'twilio.com');
    }

    private function isTelnyxUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return str_ends_with($host, 'telnyx.com');
    }
}
