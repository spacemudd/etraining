<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        return $this->belongsTo(\App\Models\User::class);
    }
}
