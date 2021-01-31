<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Str;

class CourseBatchSession extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'course_id',
        'course_batch_id',
        'trainee_group_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = [
        'starts_at_timezone',
        'ends_at_timezone',
        'can_join',
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function course_batch()
    {
        return $this->belongsTo(CourseBatch::class);
    }

    public function trainee_group()
    {
        return $this->belongsTo(TraineeGroup::class);
    }

    public function attendances()
    {
        return $this->hasMany(CourseBatchSessionAttendance::class);
    }

    public function setStartsAtAttribute($value)
    {
        $this->attributes['starts_at'] = Timezone::convertFromLocal($value);
    }

    public function setEndsAtAttribute($value)
    {
        $this->attributes['ends_at'] = Timezone::convertFromLocal($value);
    }

    public function getStartsAtTimezoneAttribute()
    {
        if ($this->starts_at) {
            return Timezone::convertToLocal($this->starts_at, 'Y-m-d H:i:s');
        }
    }

    public function getEndsAtTimezoneAttribute()
    {
        if ($this->ends_at) {
            return Timezone::convertToLocal($this->ends_at, 'Y-m-d H:i:s');
        }
    }

    public function getCanJoinAttribute()
    {
        if ($this->course_batch->location_at === 'online') {
            return now()->isBetween($this->starts_at->subMinutes(5), $this->ends_at->subMinutes(5));
        } else {
            return true; // The location_at would be a Maps link.
        }
    }
}
