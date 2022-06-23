<?php

namespace App\Models\Back;

use App\Models\TraineeBankPaymentReceipt;
use App\Models\User;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;

class Invoice extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable;
    use HasFactory;

    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_AUDIT_REQUIRED = 2; // Done by the chasers.
    const STATUS_PAYMENT_RECEIPT_REJECTED = 3;
    const STATUS_FINANCIAL_AUDIT_REQUIRED = 4; // Done by the Financial team.

    const PAYMENT_METHOD_BANK_RECEIPT = 0;
    const PAYMENT_METHOD_CREDIT_CARD = 1;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $perPage = 50;

    protected $fillable = [
        'company_id',
        'trainee_id',
        'created_by_id',
        'total_amount',
        'from_date',
        'to_date',
        'number',
        'status',
        'sub_total',
        'tax',
        'grand_total',
        'payment_method',
        'payment_reference_id',
        'paid_at',
        'trainee_bank_payment_receipt_id',
    ];

    protected $appends = [
        'number_formatted',
        'status_formatted',
        'payment_method_formatted',
        'created_at_date',
        'is_paid',
        'is_verified',
        'chase_status',
        'chase_boolean',
        'can_upload_receipt',
    ];

    protected $dates = [
        'from_date',
        'to_date',
    ];

    public $casts = [
        'created_at'  => 'datetime',
        'paid_at' => 'datetime',
        'chased_at' => 'datetime',
        'verified_at' => 'datetime',
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
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function verified_by()
    {
        return $this->belongsTo(User::class);
    }

    public function chased_by()
    {
        return $this->belongsTo(User::class);
    }

    public function trainee_bank_payment_receipt()
    {
        return $this->hasOne(TraineeBankPaymentReceipt::class, 'id', 'trainee_bank_payment_receipt_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getNumberFormattedAttribute(): string
    {
        return $this->created_at->year
            . str_pad($this->created_at->month, 2, "0", STR_PAD_LEFT)
            . "-"
            . $this->number;
    }

    public function getCreatedAtDateAttribute(): string
    {
        return $this->created_at->toDateString();
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
            case self::STATUS_PAYMENT_RECEIPT_REJECTED:
                return __('words.reject-payment-receipt');
            case self::STATUS_FINANCIAL_AUDIT_REQUIRED:
                return __('words.finance-audit-required');
            default:
                return "Unknown";
        }
    }

    public function getChaseStatusAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_UNPAID:
                return __("words.unpaid");
            case self::STATUS_PAID:
                return __('words.done');
            case self::STATUS_AUDIT_REQUIRED:
                return __("words.audit-required");
            case self::STATUS_PAYMENT_RECEIPT_REJECTED:
                return __('words.reject-payment-receipt');
            case self::STATUS_FINANCIAL_AUDIT_REQUIRED:
                return __('words.done');
            default:
                return "Unknown";
        }
    }

    public function getChaseBooleanAttribute()
    {
        return in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_FINANCIAL_AUDIT_REQUIRED,
        ]);
    }

    public function scopeIsPaid($query, bool $is_paid)
    {
        if ($is_paid) {
            return $query->whereNotNull('paid_at');
        }

        return $query->whereNull('paid_at');
    }

    public function scopePaid($query)
    {
        return $this->scopeIsPaid($query, true);
    }

    public function scopeNotPaid($query)
    {
        return $this->scopeIsPaid($query, false);
    }

    public function getPaymentMethodFormattedAttribute()
    {
        if ($this->payment_method ===  Invoice::PAYMENT_METHOD_CREDIT_CARD) return __('words.credit-card-method');
        if ($this->payment_method ===  Invoice::PAYMENT_METHOD_BANK_RECEIPT) return __('words.bank-transfer');
        return '';
    }

    public function getIsVerifiedAttribute()
    {
        if ($this->payment_method === Invoice::PAYMENT_METHOD_CREDIT_CARD && $this->paid_at) return true;
        if ($this->payment_method === Invoice::PAYMENT_METHOD_BANK_RECEIPT && $this->verified_by_id && $this->chased_by_id) return true;

        return false;
    }

    public function getCanUploadReceiptAttribute()
    {
        return $this->status === Invoice::STATUS_UNPAID || $this->status === Invoice::STATUS_PAYMENT_RECEIPT_REJECTED;
    }
}
