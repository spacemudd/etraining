<?php

namespace App\Models\Back;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Resignation extends Model implements Auditable, HasMedia
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'created_at_timezone',
        'sent_at_timezone',
        'has_file',
    ];

    protected $fillable = [
        'date',
        'number',
        'company_id',
        'emails_to',
        'emails_cc',
        'emails_bcc',
        'sent_at',
        'reason',
    ];

    protected $with = [
        'created_by',
    ];

    protected $withCount = [
        'trainees',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'sent_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->status = 'new';
                $model->team_id = auth()->user()->currentTeam()->first()->id;
                $model->created_by_id = auth()->user()->id;
            }
        });
    }

    public function trainees()
    {
        return $this->belongsToMany(Trainee::class, 'resignation_trainees', 'resignation_id', 'trainee_id')
            ->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class);
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

    /**
     *
     * @return string|void
     */
    public function getSentAtTimezoneAttribute()
    {
        if ($this->sent_at) {
            return Timezone::convertToLocal($this->sent_at, 'Y-m-d h:i A');
        }
    }

    public function getHasFileAttribute()
    {
        return $this->media()->count();
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
            ->sanitizingFileName(function ($fileName) {
                return Str::slug(Str::beforeLast($fileName, '.')) . '.' . Str::afterLast($fileName, '.');
            })
            ->withAttributes([
                'team_id' => $this->team_id,
            ])
            ->toMediaCollection($folder);
    }
}
