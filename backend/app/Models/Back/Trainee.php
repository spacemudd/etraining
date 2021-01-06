<?php

namespace App\Models\Back;

use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\InboxMessage;
use App\Models\MaritalStatus;
use Laravel\Scout\Searchable;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class Trainee extends Model implements HasMedia
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;
    use InteractsWithMedia;
    use Searchable;


    const SEARCHABLE_FIELDS = ['id', 'identity_number', 'phone', 'phone_additional', 'name', 'email'];


    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'instructor_id',
        'email',
        'name',
        'identity_number',
        'birthday',
        'educational_level_id',
        'city_id',
        'marital_status_id',
        'children_count',
        'phone',
        'phone_additional',
        'name_selectable',
    ];

    protected $appends = [
        'identity_copy_url',
        'qualification_copy_url',
        'bank_account_copy_url',
        'name_selectable',
        "show_url",
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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function educational_level()
    {
        return $this->belongsTo(EducationalLevel::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function marital_status()
    {
        return $this->belongsTo(MaritalStatus::class);
    }

    public function trainee_group()
    {
        return $this->belongsToMany(TraineeGroup::class, 'trainee_group_trainee');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id', 'id');
    }

    public function scopeResponsibleToTeach($q)
    {
        return $q->where('instructor_id', auth()->user()->instructor->id);
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
            ->withAttributes([
                'team_id' => $this->team_id,
            ])
            ->toMediaCollection($folder);
    }

    public function attachments($folder)
    {
        return $this->media()->where('collection_name', $folder);
    }

    public function getIdentityCopyUrlAttribute()
    {
        return $this->getCopyUrl('identity');
    }

    public function getQualificationCopyUrlAttribute()
    {
        return $this->getCopyUrl('qualification');
    }

    public function getBankAccountCopyUrlAttribute()
    {
        return $this->getCopyUrl('bank-account');
    }

    public function getCopyUrl($collection_name)
    {
        $media_id = optional($this->media()->where('collection_name', $collection_name)->first())->id;

        if ($media_id) {
            return route('back.media.download', ['media_id' => $media_id]);
        }
    }

    public function getShowUrlAttribute() {
        return URL("/back/trainees/{$this->id}");
    }

    public function getNameSelectableAttribute()
    {
        return $this->name.' ('.$this->identity_number.')';
    }

    public function toSearchableArray() {
        return $this->only(self::SEARCHABLE_FIELDS);
    }
}
