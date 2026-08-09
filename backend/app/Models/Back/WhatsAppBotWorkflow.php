<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class WhatsAppBotWorkflow extends Model
{
    public $incrementing = false;

    protected $table = 'whatsapp_bot_workflows';

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'is_active',
        'graph',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'graph' => 'array',
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

    public function sender(): HasOne
    {
        return $this->hasOne(WhatsAppBotSender::class, 'workflow_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(WhatsAppBotSession::class, 'workflow_id');
    }

    /**
     * @return array{nodes: array<string, array<string, mixed>>, connections: array<string, array<int, array<string, mixed>>>}
     */
    public function normalizedGraph(): array
    {
        $graph = $this->graph ?? [];

        return [
            'nodes' => is_array($graph['nodes'] ?? null) ? $graph['nodes'] : [],
            'connections' => is_array($graph['connections'] ?? null) ? $graph['connections'] : [],
        ];
    }
}
