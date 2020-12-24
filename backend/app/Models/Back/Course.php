<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Str;

class Course extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;
    use InteractsWithMedia;

    public $incrementing = false;

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
}
