<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

class WhatsAppMessage extends Model
{
    public const DIRECTION_INBOUND = 'inbound';

    public const DIRECTION_OUTBOUND = 'outbound';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'twilio_sid',
        'trainee_id',
        'phone',
        'direction',
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
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }
}
