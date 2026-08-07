<?php

declare(strict_types=1);

namespace App\Models\Back;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WhatsAppConversation extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CLOSED = 'closed';

    public $incrementing = false;

    protected $table = 'whatsapp_conversations';

    protected $keyType = 'string';

    protected $fillable = [
        'phone',
        'trainee_id',
        'status',
        'last_message_body',
        'last_message_direction',
        'last_message_is_note',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'last_message_is_note' => 'boolean',
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

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'whatsapp_conversation_agents',
            'conversation_id',
            'user_id'
        )->withPivot('assigned_at');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            WhatsAppTag::class,
            'whatsapp_conversation_tag',
            'conversation_id',
            'tag_id'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'phone', 'phone');
    }
}
