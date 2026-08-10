<?php

declare(strict_types=1);

namespace App\Models\Back;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WhatsAppQuickReply extends Model
{
    public $incrementing = false;

    protected $table = 'whatsapp_quick_replies';

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'body',
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
