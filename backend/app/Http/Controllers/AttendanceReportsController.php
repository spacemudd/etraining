<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceSheetExport;
use App\Exports\TraineeAttendanceExportByGroup;
use App\Jobs\CourseBatchSessionWarningsJob;
use App\Jobs\ExportAttendanceReportJobByCourse;
use App\Jobs\MakeAttendanceReportJob;
use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\Invoice;
use Carbon\Carbon;
use Excel;
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

        if (!$report->is_ready_for_review && !$report->job_started_at) {
            $report->job_started_at = now();
            $report->save();
            dispatch(new MakeAttendanceReportJob($course_batch_session_id, $report->id));
        }

        $paginator = AttendanceReportRecord::where('attendance_report_id', $report->id)
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }]);

        if ($request->searchTraineeName) {
            $paginator->whereHas('trainee', function($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->searchTraineeName.'%');
            });
        }

        return Inertia::render('Teaching/AttendanceReport/Show', [
            'course_batch_session' => $report->course_batch_session->load('course'),
            'attendance_report_prop' => $report,
            'attendances_count' => $paginator->count(),
            'attendances_prop' => $paginator->paginate(30),
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
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }])
            ->where('status', AttendanceReportRecord::STATUS_ABSENT);

        $sendingLateWarningsQuery = $report->attendances()
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }])
            ->where('status', AttendanceReportRecord::STATUS_LATE_TO_CLASS);

        $presentCount = $report->attendances()
            ->where('status', AttendanceReportRecord::STATUS_PRESENT)
            ->count();

        return Inertia::render('Teaching/AttendanceReport/Confirm', [
            'course_batch_session' => $report->course_batch_session,
            'report' => $report,
            'present_count' => $presentCount,
            'sending_late_warnings_to_count' => $sendingLateWarningsQuery->count(),
            'sending_late_warnings_to_list' => $sendingLateWarningsQuery->take(50)->get(),
            'sending_absent_warnings_to_count' => $sendingWarningsToQuery->count(),
            'sending_absent_warnings_to_list' => $sendingWarningsToQuery->take(50)->get(),
        ]);
    }

    public function approve($report_id)
    {
        $report = AttendanceReport::find($report_id);

        if ($report->status == AttendanceReport::STATUS_SUBMITTED_REPORT) {
            abort(404);
        }

        $report->status = AttendanceReport::STATUS_SUBMITTED_REPORT;
        $report->submitted_by = auth()->user()->id;
        $report->save();

        CourseBatchSessionWarningsJob::dispatch($report);

        return redirect()->route('teaching.courses.show', ['course' => $report->course_batch_session->course_id]);
    }

    /**
     * Download attendances in Excel format.
     *
     * @param $report_id
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excel($report_id)
    {
        $report = AttendanceReport::with('course_batch_session')->findOrFail($report_id);
        $courseName = $report->course_batch_session->course->name_ar;
        $sessionDate = $report->course_batch_session->starts_at->format('Y-m-d');
        return Excel::download(new AttendanceSheetExport($report), $sessionDate.'-'.$courseName.'-.xlsx');
    }
    

    public function exportAttendanceReportByGroup($courseBatchId)
{
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $courseBatch = CourseBatch::findOrFail($courseBatchId);

    $courseEndDate = Carbon::parse($courseBatch->ends_at);
    $startOfMonth = $courseEndDate->copy()->startOfMonth();
    $daysDifference = $courseEndDate->diffInDays($startOfMonth);

    if ($daysDifference >= 15) {
        $targetMonth = $courseEndDate->month;
        $targetYear = $courseEndDate->year;
    } else {
        $targetMonth = $courseEndDate->subMonth()->month;
        $targetYear = $courseEndDate->year;
    }
    $startOfTargetMonth = Carbon::createFromDate($targetYear, $targetMonth, 1)->startOfMonth();
    $endOfTargetMonth = Carbon::createFromDate($targetYear, $targetMonth, 1)->endOfMonth();

    $results = [];
    $totalSessionsCount = $courseBatch->course_batch_sessions()->count();

    $courseBatch->trainee_group->trainees()->chunk(100, function ($traineesChunk) use (&$results, $courseBatchId, $totalSessionsCount, $startOfTargetMonth, $endOfTargetMonth) {
        foreach ($traineesChunk as $trainee) {
            $attendanceRecords = $trainee->attendanceReportRecords()
                ->where('course_batch_id', $courseBatchId)
                ->get()
                ->unique('course_batch_session_id');

            $presentCount = $attendanceRecords->whereIn('status', [1, 2, 3])->count();
            $absentCount = $attendanceRecords->where('status', 0)->count();

            $attendancePercentage = $totalSessionsCount > 0 ? ($presentCount / $totalSessionsCount) * 100 : 0;

            $invoice = Invoice::where('trainee_id', $trainee->id)->where(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
                $query->whereBetween('from_date', [$startOfTargetMonth, $endOfTargetMonth])
                      ->orWhereBetween('to_date', [$startOfTargetMonth, $endOfTargetMonth])
                      ->orWhere(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
                          $query->where('from_date', '<=', $startOfTargetMonth)
                                ->where('to_date', '>=', $endOfTargetMonth);
                      });
            })->first();

            $invoiceStatus = $invoice ? $invoice->status_formatted : 'لا توجد فاتورة';
            $paidDate = $invoice ? $invoice->paid_at : '';
            $invoiceFromDate=$invoice ?$invoice->from_date : '' ;
            $invoiceToDate=$invoice ?$invoice->to_date : '' ;


            if (isset($trainee->name)) {
                $results[] = [
                    'paid_date' => $paidDate,
                    'invoice_to_date' => $invoiceToDate,
                    'invoice_from_date'=>$invoiceFromDate,
                    'invoice_status' => $invoiceStatus,
                    'attendance_percentage' => round($attendancePercentage, 2) . ' %',
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                    'email' => $trainee->email,
                    'phone' => $trainee->phone,
                    'identity_number' => $trainee->identity_number,
                    'trainee_name' => $trainee->name,
                ];
            }
        }
    });

         $results = collect($results)->sortByDesc(function ($trainee) {
        $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
        return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
        })->values()->toArray();

    return Excel::download(new TraineeAttendanceExportByGroup($results), 'trainee_attendance_by_group.xlsx');
}

public function exportAttendanceReportByCourse($courseId)
{
    try {
        $results = (new ExportAttendanceReportJobByCourse($courseId))->handle();

        return Excel::download(new TraineeAttendanceExportByGroup($results), 'trainee_attendance_by_courses.xlsx');
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'حدث خطأ أثناء معالجة التقرير: ' . $e->getMessage(),
        ], 500);
    }
}




}
