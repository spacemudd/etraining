<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAttendanceReportsTrainee extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    protected $fillable = [
        'status',
        'comment',
        'active',
        'start_date',
        'end_date',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    public function report()
    {
        return $this->belongsTo(CompanyAttendanceReport::class, 'company_attendance_report_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @deprecated
     */
    public function company_attendance_reports()
    {
        return $this->belongsTo(CompanyAttendanceReport::class)->withTrashed();
    }
}
