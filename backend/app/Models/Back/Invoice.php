<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_AUDIT_REQUIRED = 2;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_id',
        'trainee_id',
        'amount',
        'month',
        'number',
        'status',
        'year',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)Str::uuid();
            $model->number = MaxNumber::generatePrefixForInvoice();
        });
    }
}
