<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class CompanyContract extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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
        'instructor_cost',
        'company_reimbursement',
        'notes',
    ];

    protected $appends = [
        'has_attachments',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
                $model->created_by_id = auth()->user()->id;
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

    public function attachments()
    {
        return $this->media()->where('collection_name', 'contract_copy');
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'company_contract_instructor');
    }

    /**
     * Upload scan(s) of the contract.
     *
     * @param $file
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function addContractCopyAttachment($file)
    {
        return $this->addMedia($file)
            ->withAttributes([
                'team_id' => $this->team_id,
            ])
            ->toMediaCollection('contract_copy');
    }

    public function getHasAttachmentsAttribute()
    {
        return $this->getMedia('contract_copy')->count();
    }
}
