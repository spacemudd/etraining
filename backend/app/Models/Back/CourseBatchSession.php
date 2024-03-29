<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;
use Str;

class CourseBatchSession extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

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
        'instructor_started_at' => 'datetime',
    ];

    protected $appends = [
        'starts_at_timezone',
        'ends_at_timezone',
        'can_join',
        'can_be_deleted',
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

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function attendances()
    {
        return $this->hasMany(CourseBatchSessionAttendance::class);
    }

    public function attendance_report()
    {
        return $this->hasOne(AttendanceReport::class);
    }

    public function attendance_snapshots()
    {
        return $this->hasMany(AttendanceReportRecord::class);
    }

    public function absentees()
    {
        return $this->attendances()->where('attended', false);
    }

    public function setStartsAtAttribute($value)
    {
        $this->attributes['starts_at'] = Carbon::parse($value, optional(auth()->user())->timezone ?: config('app.timezone'))->setTimezone('UTC');
    }

    public function setEndsAtAttribute($value)
    {
        $this->attributes['ends_at'] = Carbon::parse($value, optional(auth()->user())->timezone ?: config('app.timezone'))->setTimezone('UTC');
    }

    public function getStartsAtTimezoneAttribute()
    {
        if ($this->starts_at) {
            return Timezone::convertToLocal($this->starts_at, 'Y-m-d h:i A');
        }
    }

    public function getEndsAtTimezoneAttribute()
    {
        if ($this->ends_at) {
            return Timezone::convertToLocal($this->ends_at, 'Y-m-d h:i A');
        }
    }

    public function getInstructorStartedAtTimezoneAttribute()
    {
        if ($this->instructor_started_at) {
            return Timezone::convertToLocal($this->instructor_started_at, 'Y-m-d h:i A');
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

    public function getCanBeDeletedAttribute()
    {
        return now()->isBefore($this->starts_at);
    }
}
