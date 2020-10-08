<?php

namespace App\Models\Back;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyContract extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_id',
        'number',
        'date',
        'trainees_count',
        'trainee_salary',
        'trainer_cost',
        'company_reimbursement',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (auth()->user()) {
                $model->{$model->getKeyName()} = auth()->user()->personalTeam()->id;
            }
        });
    }
}
