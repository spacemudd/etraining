<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WhatsAppBotSender extends Model
{
    public $incrementing = false;

    protected $table = 'whatsapp_bot_senders';

    protected $keyType = 'string';

    protected $fillable = [
        'phone',
        'label',
        'workflow_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(WhatsAppBotWorkflow::class, 'workflow_id');
    }
}
