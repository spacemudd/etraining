<?php

namespace App\Models;

use App\Traits\HasUuid;
use Str;

class Audit extends \OwenIt\Auditing\Models\Audit
{
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
}
