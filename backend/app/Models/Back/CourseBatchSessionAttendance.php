<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Str;

class CourseBatchSessionAttendance extends Model
{
    use HasFactory;
    use HasUuid;

    const STATUS_ABSENT = 1;
    const STATUS_ABSENT_FORGIVEN = 2;
    const STATUS_PRESENT = 3;

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
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    protected $appends = [
        'attended_at_timezone',
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
}
