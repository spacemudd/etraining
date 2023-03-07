<?php

namespace App\Models\Back;

use App\Models\SearchableLabels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;
use Str;
use Illuminate\Database\Eloquent\Builder;

class Company extends Model implements SearchableLabels, Auditable
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    const SEARCHABLE_FIELDS = ['id', 'name_ar', 'name_en', 'email', 'shelf_number'];

    protected $fillable = [
        'name_ar',
        'name_en',
        'cr_number',
        'contact_number',
        'company_rep',
        'company_rep_mobile',
        'email',
        'address',
        'monthly_subscription_per_trainee',
        'shelf_number',
        'is_ptc_net',
    ];

    protected $appends = [
        'show_url',
        'resource_label',
        'resource_type',
    ];

    protected static function boot(): void
    {
        parent::boot();
        //static::addGlobalScope(new TeamScope());

        if (!in_array(auth()->user()->email, ['sara@ptc-ksa.com', 'mashal.a+1@ptc-ksa.com', 'jawaher@ptc-ksa.net'])) {
            if (Str::contains(optional(auth()->user())->email, 'ptc-ksa.com')) {
                static::addGlobalScope('RiyadhBankAccounts', function (Builder $builder) {
                    $builder->whereNull('is_ptc_net');
                });
            }

            if (Str::contains(optional(auth()->user())->email, 'ptc-ksa.net')) {
                static::addGlobalScope('RiyadhBankAccounts', function (Builder $builder) {
                    $builder->whereNotNull('is_ptc_net');
                });
            }
        }

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->is_ptc_net = now();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    /**
     * The business contracts under a company (client).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyContract::class);
    }

    public function trainees()
    {
        return $this->hasMany(Trainee::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     *
     * @return string
     */
    public function getResourceLabelAttribute(): string
    {
        if (app()->getLocale() === 'ar') {
            return $this->name_ar;
        }

        return $this->name_en ?: $this->name_ar;
    }

    /**
     * Returns Route URL
     *
     * @return route
     */
    public function getShowUrlAttribute(): string
    {
        return route('back.companies.show', $this->id);
    }

    /**
     *
     * @return string
     */
    public function getResourceTypeAttribute(): string
    {
        return trans('words.company');
    }

    /**
     * Returns searchable fields to be used by Scout.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only(self::SEARCHABLE_FIELDS);
    }

    public function company_attendance_reports()
    {
        return $this->hasMany(CompanyAttendanceReport::class);
    }
}
