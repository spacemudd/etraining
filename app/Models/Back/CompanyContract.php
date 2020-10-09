<?php

namespace App\Models\Back;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class CompanyContract extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'contract_starts_at' => 'date:Y-m-d',
        'contract_ends_at' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'company_id',
        'reference_number',
        'contract_starts_at',
        'contract_ends_at',
        'contract_period_in_months',
        'auto_renewal',
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
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = auth()->user()->personalTeam()->id;
            }

            if ($model->contract_starts_at && $model->contract_period_in_months) {
                $start_at = Carbon::parse($model->contracts_start_at);
                $end_date = $start_at->addMonths($model->contract_period_in_months)->startOfDay()->format('Y-m-d');
                $model->contract_ends_at = $end_date;
            }
        });

        static::updating(function ($model) {
            if ($model->contract_starts_at && $model->contract_period_in_months) {
                $start_at = Carbon::parse($model->contracts_start_at);
                $end_date = $start_at->addMonths($model->contract_period_in_months)->startOfDay()->format('Y-m-d');
                $model->contract_ends_at = $end_date;
            }
        });
    }
}
