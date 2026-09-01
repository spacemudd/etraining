<?php

declare(strict_types=1);

namespace App\Models\Back;

use App\Scope\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class WhatsAppMessage extends Model implements HasMedia
{
    use InteractsWithMedia;
    public const DIRECTION_INBOUND = 'inbound';

    public const DIRECTION_OUTBOUND = 'outbound';

    public $incrementing = false;

    protected $table = 'whatsapp_messages';

    protected $keyType = 'string';

    protected $fillable = [
        'twilio_sid',
        'trainee_id',
        'user_id',
        'phone',
        'direction',
        'is_note',
        'body',
        'status',
        'from_address',
        'to_address',
        'sent_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
        'is_note' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function media(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->withoutGlobalScope(TeamScope::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('whatsapp_media')->useDisk('s3');
    }

    /**
     * @return array<int, array{url: string, id?: string, kind?: string}>
     */
    public function inboundMediaItems(): array
    {
        $metadata = is_array($this->metadata) ? $this->metadata : [];
        $media = $metadata['media'] ?? [];

        if (! is_array($media)) {
            return [];
        }

        return array_values(array_filter($media, static function ($item): bool {
            return is_array($item) && (filled($item['url'] ?? null) || filled($item['id'] ?? null));
        }));
    }

    public function inboundMediaNeedsPersist(): bool
    {
        if ($this->direction !== self::DIRECTION_INBOUND || $this->is_note) {
            return false;
        }

        if ($this->inboundMediaItems() === []) {
            return false;
        }

        return $this->getMedia('whatsapp_media')->count() < count($this->inboundMediaItems());
    }

    /**
     * @return array<int, array{id: string, url: string, name: ?string, content_type: ?string}>
     */
    public function persistedMediaPayload(): array
    {
        return $this->getMedia('whatsapp_media')->map(static function ($media): array {
            return [
                'id' => (string) $media->id,
                'url' => route('back.chat.messages.media', [
                    'id' => $this->id,
                    'media' => $media->id,
                ]),
                'name' => $media->file_name,
                'content_type' => $media->mime_type,
            ];
        })->values()->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function withPersistedMedia(array $payload): array
    {
        $saved = $this->persistedMediaPayload();
        $payload['saved_media'] = $saved;

        $metadata = is_array($payload['metadata'] ?? null) ? $payload['metadata'] : [];
        $original = is_array($metadata['media'] ?? null) ? array_values($metadata['media']) : [];

        if ($saved !== []) {
            if ($original === []) {
                $metadata['media'] = array_map(static function (array $file): array {
                    return [
                        'url' => $file['url'],
                        'name' => $file['name'],
                        'content_type' => $file['content_type'],
                    ];
                }, $saved);
            } else {
                foreach ($original as $index => $item) {
                    if (! is_array($item) || ! isset($saved[$index])) {
                        continue;
                    }
                    $original[$index]['url'] = $saved[$index]['url'];
                    $original[$index]['name'] = $saved[$index]['name'] ?? ($item['name'] ?? null);
                    $original[$index]['content_type'] = $saved[$index]['content_type']
                        ?? ($item['content_type'] ?? $item['mime_type'] ?? null);
                }
                $metadata['media'] = $original;
            }
        }

        $payload['metadata'] = $metadata !== [] ? $metadata : ($payload['metadata'] ?? null);

        return $payload;
    }
}
