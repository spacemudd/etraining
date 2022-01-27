<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvoiceItem extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'invoice_id',
        'name_en',
        'name_ar',
        'quantity',
        'sub_total',
        'tax',
        'grand_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sub_total' => 'double',
        'tax' => 'double',
        'grand_total' => 'double',
    ];

    const DEFAULT_TAX = 0.15;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
}
