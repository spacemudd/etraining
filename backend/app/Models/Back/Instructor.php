<?php

namespace App\Models\Back;

use App\Models\City;
use App\Models\InboxMessage;
use App\Models\SearchableLabels;
use App\Models\Team;
use App\Models\User;
use Laravel\Scout\Searchable;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class Instructor extends Model implements HasMedia, SearchableLabels, Auditable
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;
    use InteractsWithMedia;
    use Searchable;
    use \OwenIt\Auditing\Auditable;

    const SEARCHABLE_FIELDS = ['id', 'identity_number', 'phone', 'phone_additional', 'name', 'email'];

    const STATUS_PENDING_UPLOADING_FILES = 0;
    const STATUS_PENDING_APPROVAL = 1;
    const STATUS_APPROVED = 2;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'identity_number',
        'team_id',
        'city_id',
        'provided_courses',
        'phone',
        'email',
        'twitter_link',
    ];

    protected $appends = [
        'cv_full_copy_url',
        'cv_summary_copy_url',
        'is_pending_uploading_files',
        'is_pending_approval',
        'is_approved',
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
            } else {
                // TODO: Identify the tenant later via the domain.
                $model->team_id = Team::first()->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function trainees()
    {
        return $this->hasMany(Trainee::class);
    }

    public function trainees_contract()
    {
        return $this->hasMany(Trainee::class);
    }

    /**
     * The user that the instructor can login with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inbox_message_from()
    {
        return $this->morphMany(InboxMessage::class, 'fromable');
    }

    public function inbox_message_to()
    {
        return $this->morphMany(InboxMessage::class, 'toable');
    }

    public function zoom_account()
    {
        return $this->hasOne(ZoomAccount::class);
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

    public function getCvFullCopyUrlAttribute()
    {
        return $this->getCopyUrl('cv-full');
    }

    public function getCvSummaryCopyUrlAttribute()
    {
        return $this->getCopyUrl('cv-summary');
    }

    public function getCopyUrl($collection_name)
    {
        $media_id = optional($this->media()->where('collection_name', $collection_name)->first())->id;

        if ($media_id) {
            return route('back.media.download', ['media_id' => $media_id]);
        }
    }

    /**
     *
     * @return bool
     */
    public function isPendingApproval()
    {
        return (int) $this->status === Instructor::STATUS_PENDING_APPROVAL;
    }

    public function getIsPendingApprovalAttribute()
    {
        return $this->isPendingApproval();
    }

    /**
     * The user didn't upload their CV or required files yet.
     *
     * @return bool
     */
    public function getIsPendingUploadingFilesAttribute()
    {
        return (int) $this->status === Instructor::STATUS_PENDING_UPLOADING_FILES;
    }

    public function getIsApprovedAttribute()
    {
        return (int) $this->status === Instructor::STATUS_APPROVED;
    }

     /**
     * Returns Route URL
     *
     * @return route
     */
    public function getShowUrlAttribute(): string
    {
        return route('back.instructors.show', $this->id);
    }

    /**
     *
     * @return string
     */
    public function getResourceLabelAttribute(): string
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getResourceTypeAttribute(): string
    {
        return trans('words.instructor-singular');
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
