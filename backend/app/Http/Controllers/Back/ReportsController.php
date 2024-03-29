<?php

namespace App\Http\Controllers\Back;

use App\Exports\CourseSessionsAttendanceSummarySheetExport;
use App\Http\Controllers\Controller;
use App\Jobs\CourseAttendanceReportJob;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\JobTracker;
use App\Reports\CourseAttendanceReportFactory;
use App\Reports\ContractsReportFactory;
use App\Jobs\ContractsReportJob;
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
            'companies' => Company::get(),
            'courses' => Course::with('instructor')->get(),
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Throwable
     */
    public function generateCourseAttendanceReport(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'company_id' => 'nullable|exists:companies,id',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $tracker = new JobTracker();
        $tracker->user_id = auth()->user()->id;
        $tracker->metadata = $request->except('_token');
        $tracker->reportable_id = null;
        $tracker->reportable_type = CourseAttendanceReportFactory::class;
        $tracker->queued_at = now();
        $tracker->save();

        $tracker = $tracker->refresh();

        CourseAttendanceReportJob::dispatch($tracker);

        return $tracker;
    }

    public function generateContractsReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);


        $tracker = new JobTracker();
        $tracker->user_id = auth()->user()->id;
        $tracker->metadata = $request->except('_token');
        $tracker->reportable_id = null;
        $tracker->reportable_type = ContractsReportFactory::class;
        $tracker->queued_at = now();
        $tracker->save();

        $tracker = $tracker->refresh();

        ContractsReportJob::dispatch($tracker);

        return $tracker;
    }

    public function formContractsReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/Contracts/Index');
    }

}
