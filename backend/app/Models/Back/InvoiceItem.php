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
        'tax',
        'amount',
    ];

    protected $appends = [
        'sub_total',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount' => 'integer',
        'tax' => 'double',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)Str::uuid();
        });
    }

    public function getSubTotalAttribute()
    {
        return $this->quantity * $this->amount;
    }

    public function getTotalAttribute()
    {
        return $this->getSubTotalAttribute() + $this->tax;
    }
}
