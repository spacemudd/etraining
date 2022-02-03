<?php

namespace App\Models\Back;

use App\Models\User;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
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
    ];

    protected $appends = [
        'number_formatted',
        'status_formatted',
        'payment_method_formatted',
        'created_at_date',
        'is_paid',
    ];

    protected $dates = [
        'from_date',
        'to_date',
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
            default:
                return "Unknown";
        }
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
}
