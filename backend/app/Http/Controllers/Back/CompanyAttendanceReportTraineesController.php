<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyAttendanceReportTraineesController extends Controller
{
    /**
     * Update what action should be taken against the
     *
     * @param $report \App\Models\Back\CompanyAttendanceReport
     * @param $trainee \App\Models\Back\Trainee
     * @return void
     */
    public function update($report_id, $trainee_id)
    {
        CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $trainee_id)
            ->update([
                'status' => request()->status,
                'comment' => request()->comment,
                'start_date' => request()->start_date ? Carbon::parse(request()->start_date)->startOfDay() : null,
                'end_date' => request()->end_date ? Carbon::parse(request()->end_date)->endOfDay() : null,
            ]);

        return redirect()->back();
    }
}
