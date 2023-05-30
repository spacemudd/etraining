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
    ];

    protected $appends = [
        'number_formatted',
    ];

    protected static function boot(): void
    {
        parent::boot();

        if (Str::contains(optional(auth()->user())->email, 'ptc-ksa.com')) {
            static::addGlobalScope('RiyadhBankAccounts', function (Builder $builder) {
                $builder->whereHas('company', function ($query) {
                    $query->whereNull('is_ptc_net');
                });
            });
        }

        if (Str::contains(optional(auth()->user())->email, 'ptc-ksa.net')) {
            static::addGlobalScope('RiyadhBankAccounts', function (Builder $builder) {
                $builder->whereHas('company', function ($query) {
                    $query->whereNotNull('is_ptc_net');
                })->where('created_at', '>=', '2023-02-01');
            });
        }

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->number = MaxNumber::generatePrefixForInvoice();

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
