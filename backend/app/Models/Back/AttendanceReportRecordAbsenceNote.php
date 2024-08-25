<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Timezone;

class AttendanceReportRecordAbsenceNote extends Model implements HasMedia, Auditable
{
    use HasFactory;
    use InteractsWithMedia;
    use \OwenIt\Auditing\Auditable;

    public $appends = [
        'created_at_timezone',
        'approved_at_timezone',
        'rejected_at_timezone',
    ];

    protected $dates = [
        'created_at',
        'approved_at',
        'rejected_at',
    ];

    protected $with = [
        'files',
        'trainee',
        'trainee.company',
    ];

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

    public function getApprovedAtTimezoneAttribute()
    {
        if ($this->approved_at) {
            return Timezone::convertToLocal($this->approved_at, 'Y-m-d h:i A');
        }
    }

    public function getRejectedAtTimezoneAttribute()
    {
        if ($this->rejected_at) {
            return Timezone::convertToLocal($this->rejected_at, 'Y-m-d h:i A');
        }
    }

    public function files()
    {
        return $this->media()->where('collection_name', 'files');
    }
}
