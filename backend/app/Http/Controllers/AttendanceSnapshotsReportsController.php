<?php

namespace App\Http\Controllers;

use App\Jobs\MakeAttendanceSnapshotJob;
use App\Models\Back\AttendanceSnapshot;
use App\Models\Back\AttendanceSnapshotsReport;
use App\Models\JobTracker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceSnapshotsReportsController extends Controller
{
    public function show($course_batch_session_id)
    {
        $report = AttendanceSnapshotsReport::where('course_batch_session_id', $course_batch_session_id)
            ->first();

        if (!$report) {
            $report = new AttendanceSnapshotsReport();
            $report->course_batch_session_id = $course_batch_session_id;
            $report->status = AttendanceSnapshotsReport::STATUS_DRAFT_REPORT;
            $report->submitted_by = null;
            $report->save();
        }


        if (!$report->is_ready_for_review) {
            dispatch(new MakeAttendanceSnapshotJob($course_batch_session_id, $report->id));
        }

        return Inertia::render('Teaching/AttendanceSnapshotsReport/Show', [
            'course_batch_session' => $report->course_batch_session->load('course'),
            'attendance_snapshots_report_prop' => $report,
            'attendances_prop' => AttendanceSnapshot::where('attendance_snapshots_report_id', $report->id)->with('trainee')->paginate(5),
        ]);
    }

    public function showReport($report_id)
    {
        return AttendanceSnapshotsReport::find($report_id);
    }

    /**
     * Close attendance report for accepting new attendances.
     *
     * @param $attendance_report_id
     */
    public function close($attendance_report_id)
    {

    }
}
