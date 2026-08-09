<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WhatsAppBotSession extends Model
{
    public $incrementing = false;

    protected $table = 'whatsapp_bot_sessions';

    protected $keyType = 'string';

    protected $fillable = [
        'phone',
        'sender_phone',
        'workflow_id',
        'current_node_id',
        'context',
        'restart_pending',
    ];

    protected $casts = [
        'context' => 'array',
        'restart_pending' => 'boolean',
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
