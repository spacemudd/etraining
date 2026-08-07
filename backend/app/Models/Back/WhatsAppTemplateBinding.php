<?php

declare(strict_types=1);

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WhatsAppTemplateBinding extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'whatsapp_template_bindings';

    protected $fillable = [
        'template_sid',
        'template_name',
        'language',
        'bindings',
    ];

    protected $casts = [
        'bindings' => 'array',
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
}
