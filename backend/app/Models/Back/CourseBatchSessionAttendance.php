<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;
use Str;

class CourseBatchSessionAttendance extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    const STATUS_ABSENT = 1;
    const STATUS_ABSENT_FORGIVEN = 2;
    const STATUS_PRESENT = 3;
    const STATUS_PRESENT_LATE_TO_COURSE = 4;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'team_id',
        'course_batch_session_id',
        'course_batch_id',
        'course_id',
        'trainee_id',
        'trainee_user_id',
        'trainee_user_id',
        'session_starts_at',
        'session_ends_at',
        'attended_at',
        'attended',
        'physical_attendance',
        'last_login_at',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
        'committed_attendances_at' => 'datetime',
    ];

    protected $appends = [
        'attended_at_timezone',
        'attendance_status',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->current_team_id;
            }
        });
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    public function getAttendedAtTimezoneAttribute()
    {
        if ($this->attended_at) {
            return Timezone::convertToLocal($this->attended_at, 'Y-m-d H:i:s');
        }
    }

    public function course_batch_session()
    {
        return $this->belongsTo(CourseBatchSession::class);
    }

    public function getAttendanceStatusAttribute()
    {
        if ($this->status) {
            switch ($this->status) {
                case self::STATUS_PRESENT:
                    return 'present';
                case self::STATUS_PRESENT_LATE_TO_COURSE:
                    return 'present_late';
                case self::STATUS_ABSENT:
                    return 'absent';
                case self::STATUS_ABSENT_FORGIVEN:
                    return 'absent_forgiven';
            }
        }

        if ($this->attended_at) {
            $notLate = $this->attended_at->isBetween(
                $this->course_batch_session->starts_at->subMinutes(5),
                $this->course_batch_session->starts_at->addMinutes(15),
            );

            return $notLate ? 'present' : 'present_late';
        }

        return 'absent';
    }
}
