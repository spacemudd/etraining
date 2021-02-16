<?php

namespace App\Models\Back;

use App\Models\User;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Str;

class MonthlyInvoicingBatch extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    const JOB_STATUS_QUEUED = 0;
    const JOB_STATUS_PROCESSING = 1;
    const JOB_STATUS_FAILED = 3;
    const JOB_STATUS_COMPLETED = 4;

    const STATUS_DRAFT = 0;
    const STATUS_APPROVED = 1;

    protected $fillable = [
        'invoices_date',
        'period_from',
        'period_to',
        'job_status',
        'status',
        'progress',
        'total',
    ];

    protected $casts = [
        'invoices_date' => 'date:Y-m-d',
        'period_from' => 'date:Y-m-d',
        'period_to' => 'date:Y-m-d',
        'status' => 'int',
    ];

    protected $appends = [
        'status_display',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->current_team_id;
                $model->created_by_id = auth()->user()->id;
            }
        });
    }

    public function created_by()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     * @return string|null
     */
    public function getStatusDisplayAttribute()
    {
        if ($this->status === self::STATUS_DRAFT) {
            return __('words.draft');
        }

        if ($this->status === self::STATUS_APPROVED) {
            return __('words.approved');
        }

        return '';
    }

    public function getIsDraftAttribute()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getFinishedGeneratingDraftInvoicesAttribute()
    {
        return $this->progress === $this->total;
    }

    public function sale_invoices()
    {
        return $this->hasMany(SaleInvoice::class);
    }
}
