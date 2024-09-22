<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;

class AttendanceReportRecordWarning extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'created_at_timezone',
    ];

    protected $dates = [
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

    public function attendance_report_record()
    {
        return $this->belongsTo(AttendanceReportRecord::class);
    }

    public function getCreatedAtTimezoneAttribute()
    {
        if ($this->created_at) {
            return Timezone::convertToLocal($this->created_at, 'Y-m-d h:i A');
        }
    }
}
