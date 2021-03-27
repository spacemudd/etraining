<?php

namespace App\Http\Controllers\Back;

use App\Exports\CourseSessionsAttendanceSummarySheetExport;
use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use App\Reports\CourseAttendanceReportFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/Index');
    }

    /**
     * View form for course attendance report.
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function formCourseAttendanceReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/CourseAttendance/Index', [
            'courses' => Course::with('instructor')->get(),
        ]);
    }

    public function generateCourseAttendanceReport(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        return CourseAttendanceReportFactory::new()
            ->setStartDate(Carbon::parse($request->date_from)->startOfDay())
            ->setEndDate(Carbon::parse($request->date_to)->endOfDay())
            ->setCourseId($request->course_id)
            ->toExcel();
    }
}
