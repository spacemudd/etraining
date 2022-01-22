<?php

namespace App\Models\Back;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_AUDIT_REQUIRED = 2;

    const PAYMENT_METHOD_BANK_RECEIPT = 0;
    const PAYMENT_METHOD_CREDIT_CARD = 1;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_id',
        'trainee_id',
        'created_by_id',
        'total_amount',
        'month',
        'number',
        'status',
        'year',
    ];

    protected $appends = [
        'number_formatted',
        'status_formatted',
        'created_at_date',
        'date_period',
        'date_period_start',
        'date_period_end',
        'is_paid',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)Str::uuid();
            $model->number = MaxNumber::generatePrefixForInvoice();

            if (auth()->check()) {
                $model->created_by_id = auth()->id();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getNumberFormattedAttribute(): string
    {
        return $this->year
            . str_pad($this->month, 2, "0", STR_PAD_LEFT)
            . "-"
            . $this->number;
    }

    public function getCreatedAtDateAttribute(): string
    {
        return $this->created_at->toDateString();
    }

    public function getDatePeriodAttribute(): Carbon
    {
        return Carbon::create($this->year, $this->month);
    }

    public function getDatePeriodStartAttribute(): string
    {
        return $this->getDatePeriodAttribute()->startOfMonth()->toDateString();
    }

    public function getDatePeriodEndAttribute(): string
    {
        return $this->getDatePeriodAttribute()->endOfMonth()->toDateString();
    }

    public function getIsPaidAttribute(): bool
    {
        return !empty($this->paid_at);
    }

    public function getStatusFormattedAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_UNPAID:
                return __("words.unpaid");
            case self::STATUS_PAID:
                return __("words.paid");
            case self::STATUS_AUDIT_REQUIRED:
                return __("words.audit-required");
            default:
                return "Unknown";
        }
    }
}
