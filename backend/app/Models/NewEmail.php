<?php

namespace App\Models;

use App\Models\Back\MaxNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable;

class NewEmail extends Model
{
    use Auditable;
    use HasFactory;

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

}
