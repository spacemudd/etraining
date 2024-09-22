<?php

namespace App\Models\Back;

use App\Mail\CompanyAttendanceFailureMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Facades\Timezone;

class CompanyAttendanceReportsEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'type',
        'clicked_confirmed_at',
        'delivered_at',
        'opened_at',
        'failed_at',
        'failed_reason',
    ];

    protected $appends = [
        'delivered_at_timezone',
    ];

    public $dates = [
        'delivered_at',
        'failed_at',
        'opened_at',
    ];

    public function report()
    {
        return $this->belongsTo(CompanyAttendanceReport::class, 'company_attendance_report_id');
    }

    /**
     *
     * @return string|void
     */
    public function getDeliveredAtTimezoneAttribute()
    {
        if ($this->delivered_at) {
            return Timezone::convertToLocal($this->delivered_at, 'Y-m-d h:i A').' GMT+3';
        }
    }

        /**
     *
     * @return string|void
     */
    public function getFailedAtTimezoneAttribute()
    {
        if ($this->failed_at) {
            return Timezone::convertToLocal($this->failed_at, 'Y-m-d h:i A');
        }
    }
}
