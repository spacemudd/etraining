<?php

namespace App\Http\Controllers\Back;

use App\Exports\CompanyInvoicesSummaryExport;
use App\Exports\TraineeAttendanceExportByGroup;
use App\Exports\TraineesWithoutInvoicesExport;
use App\Http\Controllers\Controller;
use App\Jobs\BulkCourseAttendanceReportJob;
use App\Jobs\ContractsReportJob;
use App\Jobs\CourseAttendanceReportJob;
use App\Jobs\GenerateAttendanceReportJob;
use App\Jobs\GenerateCompanyCertificatesReportJob;
use App\Models\AttendanceReportDueDates;
use App\Models\Back\Audit;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\Invoice;
use App\Models\JobTracker;
use App\Models\User;
use App\Reports\BulkCourseAttendanceReportFactory;
use App\Reports\ContractsReportFactory;
use App\Reports\CourseAttendanceReportFactory;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    public function formCompanyCertificateseReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/Certificates/CompanyCertificates', [
            'companies' => Company::get(),
            'courses' =>Course::whereIn('id', function($query) {
                $query->selectRaw('max(id)')
                      ->from('courses')
                      ->groupBy('name_ar');
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return JobTracker
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


    public function formTraineesWithoutInvoicesReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/TraineesWithoutInvoices/Index');
    }
    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);
        $data=$request->all();


        Audit::create([
            'event' => 'traineesWithoutInvoices.export.excel',
            'auditable_id' => auth()->user()->id,
            'auditable_type' => User::class,
            'new_values' => [],
        ]);

        return Excel::download(new TraineesWithoutInvoicesExport($data), now()->format('Y-m-d').'-traineees-without-invoices.xlsx');

    }

    public function formCompanyInvoicesSummaryReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/CompanyInvoicesSummary/Index');
    }

    public function exportCompanyInvoicesSummary(Request $request)
    {
        $this->authorize('view-backoffice-reports');
        
        $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);
        $data = $request->all();

        Audit::create([
            'event' => 'companyInvoicesSummary.export.csv',
            'auditable_id' => auth()->user()->id,
            'auditable_type' => User::class,
            'new_values' => [],
        ]);

        $excel = new \App\Exports\CompanyInvoicesSummaryExport($data);
        
        return Excel::download(
            $excel,
            now()->format('Y-m-d') . '-company-invoices-summary.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function formCertificatesIssuedReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/CertificatesIssued/Index');
    }

    public function exportCertificatesIssued(Request $request)
    {
        $this->authorize('view-backoffice-reports');

        Audit::create([
            'event' => 'certificatesIssued.export.excel',
            'auditable_id' => auth()->user()->id,
            'auditable_type' => User::class,
            'new_values' => [],
        ]);

        return Excel::download(new \App\Exports\CertificatesIssuedExport(), now()->format('Y-m-d').'-certificates-issued.xlsx');
    }


    public function formCompanyCertificateseGenerateReport(Request $request)
    {
        
        GenerateCompanyCertificatesReportJob::dispatch($request->all(), auth()->id());
        return response()->json(['message' => 'Report generation started.']);

        // $course = Course::find($request->input('courseId.id'));

        // if (!$course) {
        //     return response()->json(['error' => 'Course not found'], 404);
        // }

        // $courseName = $course->name_ar;
        // $courses = Course::where('name_ar', $courseName)->get();

        // $results = [];
        // ini_set('memory_limit', '512M');
        // set_time_limit(300);

        // $companyId = $request->input('companyId.id');

        // $companyName = '';

        // foreach ($courses as $course) {
        //     $batches = $course->batches;

        //     foreach ($batches as $batch) {
        //         $courseEndDate = Carbon::parse($batch->ends_at);
        //         $startOfMonth = $courseEndDate->copy()->startOfMonth();
        //         $daysDifference = $courseEndDate->diffInDays($startOfMonth);

        //         if ($daysDifference >= 15) {
        //             $targetMonth = $courseEndDate->month;
        //             $targetYear = $courseEndDate->year;
        //         } else {
        //             $targetMonth = $courseEndDate->subMonth()->month;
        //             $targetYear = $courseEndDate->year;
        //         }

        //         $startOfTargetMonth = Carbon::createFromDate($targetYear, $targetMonth, 1)->startOfMonth();
        //         $endOfTargetMonth = Carbon::createFromDate($targetYear, $targetMonth, 1)->endOfMonth();

        //         $totalSessionsCount = $batch->course_batch_sessions()->count();

        //         $traineesQuery = $batch->trainee_group->traineesWithTrashed();

        //         if ($companyId) {
        //             $traineesQuery
        //                 ->where('company_id', $companyId);
        //             $company = Company::find($companyId);
        //             if ($company) {
        //                 $companyName = $company->name_ar;
        //             }
        //         }

        //         $traineesQuery
        //             ->with('user')
        //             ->with('company')
        //             ->chunk(100, function ($traineesChunk) use (
        //             &$results,
        //             $batch,
        //             $totalSessionsCount,
        //             $startOfTargetMonth,
        //             $endOfTargetMonth,
        //             $courseName,
        //             $companyName,
        //             $companyId
        //         ) {
        //             foreach ($traineesChunk as $trainee) {
        //                 $attendanceRecords = $trainee->attendanceReportRecords()
        //                     ->where('course_batch_id', $batch->id)
        //                     ->get()
        //                     ->unique('course_batch_session_id');

        //                 $presentCount = $attendanceRecords->whereIn('status', [1, 2, 3])->count();
        //                 $absentCount = $attendanceRecords->where('status', 0)->count();

        //                 $attendancePercentage = $totalSessionsCount > 0 ? ($presentCount / $totalSessionsCount) * 100 : 0;

        //                 $invoiceQuery = Invoice::where('trainee_id', $trainee->id);

        //                 if ($companyId) {
        //                     $invoiceQuery->where('company_id', $companyId);
        //                 }

        //                 $invoice = $invoiceQuery->where(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
        //                     $query->whereBetween('from_date', [$startOfTargetMonth, $endOfTargetMonth])
        //                         ->orWhereBetween('to_date', [$startOfTargetMonth, $endOfTargetMonth])
        //                         ->orWhere(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
        //                             $query->where('from_date', '<=', $startOfTargetMonth)
        //                                 ->where('to_date', '>=', $endOfTargetMonth);
        //                         });
        //                 })->first();

        //                 $invoiceStatus = $invoice ? $invoice->status_formatted : 'لا توجد فاتورة';
        //                 $paidDate = $invoice ? $invoice->paid_at : '';
        //                 $invoiceFromDate = $invoice ? $invoice->from_date : '';
        //                 $invoiceToDate = $invoice ? $invoice->to_date : '';

        //                 $traineeCompanyName = $trainee->company ? $trainee->company->name_ar : 'غير مربوط بشركة';

        //                 if (isset($trainee->name)) {
        //                     $results[] = [
        //                         'paid_date' => $paidDate,
        //                         'invoice_to_date' => $invoiceToDate,
        //                         'invoice_from_date' => $invoiceFromDate,
        //                         'invoice_status' => $invoiceStatus,
        //                         'attendance_percentage' => round($attendancePercentage, 2) . ' %',
        //                         'present_count' => $presentCount,
        //                         'absent_count' => $absentCount,
        //                         'course_name' => $courseName,
        //                         'company_name' => $traineeCompanyName,

        //                         'email' => $trainee->email,
        //                         'phone' => $trainee->phone,
        //                         'identity_number' => $trainee->identity_number,
        //                         'trainee_name' => $trainee->name,
        //                         'deleted_at' => $trainee->deleted_at,
        //                         'last_login_at' => optional($trainee->user)->last_login_at,
        //                     ];
        //                 }
        //             }
        //         });
        //     }
        // }

        // $results = collect($results)->sortByDesc(function ($trainee) {
        //     $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
        //     return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
        // })->values()->toArray();

        // $fileName = $companyName ? $companyName . '_attendance_report.xlsx' : 'trainee_attendance_by_course.xlsx';
        // // dd($fileName);

        // return Excel::download(new TraineeAttendanceExportByGroup($results), $fileName);
    }

    public function formBulkCourseAttendanceReport()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Reports/BulkCourseAttendance/Index', [
            'companies' => Company::get(),
            'courses' => Course::with('instructor')->get(),
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return JobTracker
     * @throws \Throwable
     */
    public function generateBulkCourseAttendanceReport(Request $request)
    {
        $request->validate([
            'courses_ids.*' => 'required|exists:courses,id',
            'company_id' => 'nullable|exists:companies,id',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $tracker = new JobTracker();
        $tracker->user_id = auth()->user()->id;
        $tracker->metadata = $request->except('_token');
        $tracker->reportable_id = null;
        $tracker->reportable_type = BulkCourseAttendanceReportFactory::class;
        $tracker->queued_at = now();
        $tracker->save();

        $tracker = $tracker->refresh();

        BulkCourseAttendanceReportJob::dispatch($tracker);

        return $tracker;
    }


    public function attendanceDueDates()
    {
        $companies = Company::get();
        $coursesNames = Course::select('name_ar')
            ->orderBy('created_at', 'desc')
            ->groupBy('name_ar')
            ->get();
        $report = AttendanceReportDueDates::where('user_id', auth()->id())->latest()->first();

        return Inertia::render('Back/Reports/AttendanceDueDates/Show', [
            'companies' => $companies,
            'coursesNames' => $coursesNames,
            'latestReport' => $report,
        ]);
    }

    public function attendanceDueDatesReport(Request $request)
    {
        $report = AttendanceReportDueDates::create([
            'user_id' => auth()->id(),
            'filename' => '', // مؤقتاً
            'course_name' => $request->course_name,
            'company_id' => $request->company_id,
            'status' => 'generating',
        ]);
    
        GenerateAttendanceReportJob::dispatch(
            $request->course_name,
            $request->start_date,
            $request->end_date,
            $request->company_id,
            $report->id // نمرر ID التقرير للـ Job
        );
    
        return redirect()->route('attendance-due-dates.index')
            ->with('report_id', $report->id);
    }

    public function downloadAttendanceReport($filename)
    {
        $path = 'reports/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($path);
    }

}




