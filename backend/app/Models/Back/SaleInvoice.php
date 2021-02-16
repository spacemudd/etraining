<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use Str;

class SaleInvoice extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HasUuid;
    use Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    const STATUS_DRAFT = 0;
    const STATUS_ISSUED = 1;
    const STATUS_VOID = 2;

    protected $fillable = [
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'date:d-m-Y',
    ];

    protected $appends = [
        'grand_total_display',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    public function billable()
    {
        return $this->morphTo();
    }

    public function getGrandTotalDisplayAttribute()
    {
        return str_replace('SAR', '', Money::ofMinor($this->grand_total ?: 0, 'SAR')->formatTo('en_SA'));
    }
}
