<?php

namespace App\Models\Back;

use App\Models\SearchableLabels;
use App\Scope\TeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Str;

class Company extends Model implements SearchableLabels
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    public $incrementing = false;

    protected $keyType = 'string';

    const SEARCHABLE_FIELDS = ['id', 'name_ar', 'name_en', 'email'];

    protected $fillable = [
        'name_ar',
        'name_en',
        'cr_number',
        'contact_number',
        'company_rep',
        'company_rep_mobile',
        'email',
        'address',
    ];

    protected $appends = [
        'show_url',
        'resource_label',
        'resource_type',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
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

    /**
     *
     * @return string
     */
    public function getResourceLabelAttribute(): string
    {
        if (app()->getLocale() === 'ar') {
            return $this->name_en;
        }

        return $this->name;
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
}
