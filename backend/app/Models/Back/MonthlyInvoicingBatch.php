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
    const JOB_STATUS_SENDING_EMAILS = 5;
    const JOB_STATUS_COMPLETED_SENDING_EMAILS = 6;
    const JOB_STATUS_COMMITTING_PROCESSING = 7;
    const JOB_STATUS_COMMITTING_COMPLETED = 8;

    const STATUS_DRAFT = 0;
    const STATUS_APPROVED = 1;

    protected $fillable = [
        'company_id',
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
        'job_status_display',
        'is_processing',
        'is_processing_reason',
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

    public function sale_invoices()
    {
        return $this->hasMany(SaleInvoice::class);
    }

    public function getJobStatusDisplayAttribute()
    {
        if ($this->job_status === self::JOB_STATUS_QUEUED) {
            return 'queued';
        }

        if ($this->job_status === self::JOB_STATUS_PROCESSING) {
            return 'processing';
        }

        if ($this->job_status === self::JOB_STATUS_COMPLETED) {
            return 'completed_draft_invoices';
        }

        if ($this->job_status === self::JOB_STATUS_SENDING_EMAILS) {
            return 'sending_emails';
        }

        if ($this->job_status === self::JOB_STATUS_COMPLETED_SENDING_EMAILS) {
            return 'completed_sending_emails';
        }

        if ($this->job_status === self::JOB_STATUS_COMMITTING_PROCESSING) {
            return 'committing_processing';
        }

        if ($this->job_status === self::JOB_STATUS_COMMITTING_COMPLETED) {
            return 'committing_completed';
        }

        return '';
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

    public function getFinishedIssuingInvoicesAttribute()
    {
        return (
            $this->job_status === self::JOB_STATUS_COMMITTING_COMPLETED ||
            $this->job_status === self::JOB_STATUS_SENDING_EMAILS ||
            $this->job_status === self::JOB_STATUS_COMPLETED_SENDING_EMAILS
        );
    }

    public function getFinishedSendingInvoicesAttribute()
    {
        return (
            $this->job_status === self::JOB_STATUS_COMPLETED_SENDING_EMAILS
        );
    }

    public function getIsProcessingAttribute()
    {
        return ($this->job_status === self::JOB_STATUS_QUEUED ||
            $this->job_status === self::JOB_STATUS_COMMITTING_PROCESSING ||
            $this->job_status === self::JOB_STATUS_SENDING_EMAILS);
    }

    public function getIsProcessingReasonAttribute()
    {
        if ($this->job_status === self::JOB_STATUS_QUEUED) {
            return __('words.generating-draft-invoices');
        }

        if ($this->job_status === self::JOB_STATUS_COMMITTING_PROCESSING) {
            return __('words.generating-invoices');
        }

        if ($this->job_status === self::JOB_STATUS_SENDING_EMAILS) {
            return __('words.sending-invoices-via-email');
        }

        return '';
    }
}
