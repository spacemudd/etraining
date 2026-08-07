<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class WhatsAppTag extends Model
{
    public $incrementing = false;

    protected $table = 'whatsapp_tags';

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'color',
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

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(
            WhatsAppConversation::class,
            'whatsapp_conversation_tag',
            'tag_id',
            'conversation_id'
        );
    }
}
