<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;

class AttendanceReportRecord extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    public $guarded = ['id'];

    public $incrementing = false;

    protected $keyType = 'string';

    const STATUS_ABSENT = 0;
    const STATUS_ABSENT_WITH_EXCUSE = 1;
    const STATUS_LATE_TO_CLASS = 2;
    const STATUS_PRESENT = 3;

    protected $appends = [
        'status_name',
        'status_color',
        'attended_at_timezone',
    ];

    protected $dates = [
        'attended_at',
        'created_at',
        'updated_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($audit) {
            $audit->{$audit->getKeyName()} = (string) Str::uuid();

            if (auth()->user()) {
                $audit->team_id = auth()->user()->current_team_id;
            }
        });

        static::addGlobalScope(new TeamScope());
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    /**
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        $status = '';

        switch ($this->status) {
            case self::STATUS_ABSENT:
                $status = 'absent';
                break;
            case self::STATUS_ABSENT_WITH_EXCUSE;
                $status = 'absent-with-excuse';
                break;
            case self::STATUS_LATE_TO_CLASS:
                $status = 'present-but-late';
                break;
            case self::STATUS_PRESENT:
                $status = 'present';
                break;
        }

        return $status;
    }

    /**
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $color = '';

        switch ($this->status) {
            case self::STATUS_ABSENT:
                $color = 'red';
                break;
            case self::STATUS_ABSENT_WITH_EXCUSE;
                $color = 'blue';
                break;
            case self::STATUS_LATE_TO_CLASS:
                $color = 'purple';
                break;
            case self::STATUS_PRESENT:
                $color = 'darkgreen';
                break;
        }

        return $color;
    }

    public function getAttendedAtTimezoneAttribute()
    {
        if ($this->attended_at) {
            return Timezone::convertToLocal($this->attended_at, 'Y-m-d H:i:s');
        }
    }

    public function warnings()
    {
        return $this->hasMany(AttendanceReportRecordWarning::class);
    }
}
