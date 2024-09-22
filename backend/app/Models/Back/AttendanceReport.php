<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class AttendanceReport extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    const STATUS_DRAFT_REPORT = 1;
    const STATUS_SUBMITTED_REPORT = 2;

    protected $fillable = [
        'course_batch_session_id',
        'is_ready_for_review',
        'status',
    ];

    protected $appends = [
        'status_name',
        'can_prepare_attendance',
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

    public function course_batch_session()
    {
        return $this->belongsTo(CourseBatchSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(AttendanceReportRecord::class);
    }

    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT_REPORT:
                return 'draft';
            case self::STATUS_SUBMITTED_REPORT:
                return 'approved';
        }
    }

    public function getCanPrepareAttendanceAttribute()
    {
        return $this->course_batch_session->ends_at->isBefore(now());
    }
}
