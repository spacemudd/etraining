<?php

namespace App\Http\Controllers;

use App\Jobs\CourseBatchSessionWarningsJob;
use App\Jobs\MakeAttendanceReportJob;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReport;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceReportsController extends Controller
{
    public function show($course_batch_session_id, Request $request)
    {
        $request->validate([
            'searchTraineeName' => 'nullable|string|max:255',
        ]);

        $report = AttendanceReport::where('course_batch_session_id', $course_batch_session_id)
            ->first();

        if (!$report) {
            $report = new AttendanceReport();
            $report->course_batch_session_id = $course_batch_session_id;
            $report->status = AttendanceReport::STATUS_DRAFT_REPORT;
            $report->submitted_by = null;
            $report->save();
        }

        if (!$report->is_ready_for_review) {
            dispatch(new MakeAttendanceReportJob($course_batch_session_id, $report->id));
        }

        $paginator = AttendanceReportRecord::where('attendance_report_id', $report->id)
            ->with('trainee');

        if ($request->searchTraineeName) {
            $paginator->whereHas('trainee', function($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->searchTraineeName.'%');
            });
        }

        return Inertia::render('Teaching/AttendanceReport/Show', [
            'course_batch_session' => $report->course_batch_session->load('course'),
            'attendance_report_prop' => $report,
            'attendances_prop' => $paginator->paginate(20),
        ]);
    }

    public function showReport($report_id)
    {
        return AttendanceReport::find($report_id);
    }

    /**
     * Close attendance report for accepting new attendances.
     *
     * @param $attendance_report_id
     */
    public function close($attendance_report_id)
    {

    }

    /**
     * show attendances
     *
     * @param \Illuminate\Http\Request $request
     */
    public function attendances(Request $request)
    {
        $paginator = AttendanceReportRecord::where('attendance_report_id', $request->attendance_report_id)
            ->with('trainee');

        if ($request->searchTraineeName) {
            $paginator->whereHas('trainee', function($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->searchTraineeName.'%');
            });
        }

        return $paginator->paginate(20);
    }

    public function confirm($report_id)
    {
        $report = AttendanceReport::with(['course_batch_session' => function($q) {
            $q->with('course');
        }])->find($report_id);

        $sendingWarningsToQuery = $report->attendances()
            ->with('trainee')
            ->where('status', AttendanceReportRecord::STATUS_ABSENT);

        $sendingLateWarningsQuery = $report->attendances()
            ->with('trainee')
            ->where('status', AttendanceReportRecord::STATUS_LATE_TO_CLASS);

        return Inertia::render('Teaching/AttendanceReport/Confirm', [
            'course_batch_session' => $report->course_batch_session,
            'report' => $report,
            'sending_absent_warnings_to_count' => $sendingWarningsToQuery->count(),
            'sending_absent_warnings_to_list' => $sendingWarningsToQuery->take(10)->get(),
            'sending_late_warnings_to_count' => $sendingLateWarningsQuery->count(),
            'sending_late_warnings_to_list' => $sendingLateWarningsQuery->take(10)->get(),
        ]);
    }

    public function approve($report_id)
    {
        $report = AttendanceReport::find($report_id);
        CourseBatchSessionWarningsJob::dispatch($report);
        return redirect()->route('teaching.courses.show', ['course' => $report->course_batch_session->course_id]);
    }
}
