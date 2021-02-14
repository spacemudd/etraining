<?php

namespace App\Models\Back;

use App\Models\Back\CourseBatch;
use App\Models\SearchableLabels;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use Str;

class Course extends Model implements HasMedia, SearchableLabels
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;
    use Searchable;
    use InteractsWithMedia;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    public $incrementing = false;

    const SEARCHABLE_FIELDS = ['id', 'description', 'name_ar', 'name_en'];

    protected $keyType = 'string';

    protected $fillable = [
        'name_ar',
        'name_en',
        'instructor_id',
        'company_id',
        'description',
        'classroom_count',
        'approval_code',
        'days_duration',
        'hours_duration',
    ];

    protected $appends = [
        'training_package_url',
        'is_pending_approval',
        'show_url',
        'is_approved',
        'resource_label',
        'resource_type',
        'can_show_certificate',
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

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function batches()
    {
        return $this->hasMany(CourseBatch::class);
    }

    public function scopeResponsibleToTeach($q)
    {
        return $q->where('instructor_id', auth()->user()->instructor->id);
    }

    /**
     * Scope for limiting the trainees to viewing only
     * the courses they're currently attending.
     *
     * @param $q
     * @return mixed
     */
    public function scopeAttending($q)
    {
        $instructor_id = optional(auth()->user()->trainee)->instructor_id;
        return $q->where('instructor_id', $instructor_id);
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


    public function getTrainingPackageUrlAttribute()
    {
        return $this->getCopyUrl('training-package');
    }

    public function getCopyUrl($collection_name)
    {
        $media_id = optional($this->media()->where('collection_name', $collection_name)->first())->id;

        if ($media_id) {
            return route('back.media.download', ['media_id' => $media_id]);
        }
    }

    public function getIsPendingApprovalAttribute()
    {
        return (int) $this->status === self::STATUS_PENDING;
    }

    public function getIsApprovedAttribute()
    {
        return (int) $this->status === self::STATUS_APPROVED;
    }


    public function toSearchableArray()
    {
        return $this->only(self::SEARCHABLE_FIELDS);
    }

    public function getShowUrlAttribute(): string
    {
        if ($this->id) {
            return route('back.courses.show', ['course' => $this->id]);
        }

        return '';
    }

    /**
     *
     * @return string
     */
    public function getResourceLabelAttribute(): string
    {
        return $this->name_ar.' ('.$this->name_en.')';
    }

    /**
     *
     * @return string
     */
    public function getResourceTypeAttribute(): string
    {
        return trans('words.course');
    }

    public function getCanShowCertificateAttribute() {

        $can_show_certificate = true;

        $course_batches = CourseBatch::where('course_id', $this->id)->get()->toArray();

        foreach ($course_batches as $course_batch) {
            if(strtotime($course_batch['ends_at']) <= time()) {
                continue;
            } else {
                $can_show_certificate = false;
                break;
            }
        }

        return $can_show_certificate;
    }
}

