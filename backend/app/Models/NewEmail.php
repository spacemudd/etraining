<?php

namespace App\Models;

use App\Models\Back\MaxNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewEmail extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;


    protected $keyType = 'string';

    protected $fillable = [
        'created_by_id',
        'number',
        'applicant',
        'personal_email',
        'phone',
        'job_title',
        'manager_name',
        'manager_email',
        'new_email',
        'rejected_reason',
    ];

    protected $appends = [
        'number_formatted',
        'status_formatted',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->number = MaxNumber::generatePrefixForNewEmail();

            if (auth()->check()) {
                $model->created_by_id = auth()->id();
            }
        });
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getNumberFormattedAttribute(): string
    {
        return $this->created_at->year
            . str_pad($this->created_at->month, 2, "0", STR_PAD_LEFT)
            . "-"
            . $this->number;
    }

    public function getStatusFormattedAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return __("words.pending");
            case self::STATUS_APPROVED:
                return __("words.approved");
            case self::STATUS_REJECTED:
                return __("words.rejected");
            default:
                return "Unknown";
        }
    }

}
