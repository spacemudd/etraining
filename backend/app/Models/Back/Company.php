<?php

namespace App\Models\Back;

use App\Models\SearchableLabels;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;
use Str;
use Illuminate\Database\Eloquent\Builder;
use Timezone;


use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements SearchableLabels, Auditable, HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;


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
        'salesperson_email',
        'region_id',
        'center_id',
        'recruitment_company_id',
    ];

    protected $appends = [
        'show_url',
        'resource_label',
        'resource_type',
        'created_at_timezone',
    ];

    protected static function boot(): void
    {
        parent::boot();
        //static::addGlobalScope(new TeamScope());

        if (!in_array(optional(auth()->user())->email, ['sara@ptc-ksa.net', 'mashael.a@ptc-ksa.net', 'jawaher@ptc-ksa.net'])) {
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

        //if (auth()->user()) {
        //    if (!auth()->user()->can('view-all-companies')) {
        //        static::addGlobalScope('companyAllowedUsers', function (Builder $builder) {
        //            $builder->whereHas('allowed_users', function ($q) {
        //                $q->where('user_id', auth()->user()->id);
        //            });
        //        });
        //    }
        //}

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            $model->code = 'C'.MaxNumber::generateForPrefix('C', 1000);
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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function recruitmentCompany()
    {
        return $this->belongsTo(RecruitmentCompany::class);
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


    /**
     * Upload scan(s) of the documents.
     *
     * @param $file
     * @param $folder
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function uploadToFolder($file, $folder)
    {
        return $this->addMedia($file)
            ->usingFileName($file->hashName())
            ->withAttributes([
                'team_id' => $this->team_id,
            ])
            ->toMediaCollection($folder);
    }

    public function attachments($folder)
    {
        return $this->media()->where('collection_name', $folder);
    }

    public function logo_files()
    {
        return $this->media()->where('collection_name', 'logo_files');
    }

    public function company_attendance_reports()
    {
        return $this->hasMany(CompanyAttendanceReport::class);
    }

    public function resignations()
    {
        return $this->hasMany(Resignation::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allowed_users()
    {
        return $this->belongsToMany(User::class, 'company_allowed_users', 'company_id', 'user_id');
    }

    /**
     *
     * @return string|void
     */
    public function getCreatedAtTimezoneAttribute()
    {
        if ($this->created_at) {
            return Timezone::convertToLocal($this->created_at, 'Y-m-d h:i A');
        }
    }

    public function company_mails()
    {
        return $this->hasMany(CompanyMail::class)->latest();
    }

    /**
     * Different names for the company that matches with GOSI.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aliases()
    {
        return $this->hasMany(CompanyAlias::class);
    }
}
