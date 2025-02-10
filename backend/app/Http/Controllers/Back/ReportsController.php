<?php

namespace App\Http\Controllers\Back;

use App\Exports\CourseSessionsAttendanceSummarySheetExport;
use App\Exports\TraineeAttendanceExportByGroup;
use App\Exports\TraineesWithoutInvoicesExport;
use App\Http\Controllers\Controller;
use App\Jobs\ContractsReportJob;
use App\Jobs\CourseAttendanceReportJob;
use App\Models\Back\Audit;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\JobTracker;

use App\Models\User;
use App\Reports\ContractsReportFactory;
use App\Reports\CourseAttendanceReportFactory;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Excel;



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


    public function formCompanyCertificateseGenerateReport(Request $request)
    {
        $course = Course::find($request->input('courseId.id'));
    
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }
    
        $courseName = $course->name_ar;
        $courses = Course::where('name_ar', $courseName)->get();
    
        $results = [];
        ini_set('memory_limit', '512M');
        set_time_limit(300);
    
        $companyId = $request->input('companyId.id'); 
        
        foreach ($courses as $course) {
            $batches = $course->batches;
    
            foreach ($batches as $batch) {
                $courseEndDate = Carbon::parse($batch->ends_at);
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
    
                $totalSessionsCount = $batch->course_batch_sessions()->count();
    
                $traineesQuery = $batch->trainee_group->trainees();
    
                if ($companyId) {
                    $traineesQuery->where('company_id', $companyId);
                }
    
                $traineesQuery->chunk(100, function ($traineesChunk) use (
                    &$results,
                    $batch,
                    $totalSessionsCount,
                    $startOfTargetMonth,
                    $endOfTargetMonth,
                    $companyId
                ) {
                    foreach ($traineesChunk as $trainee) {
                        $attendanceRecords = $trainee->attendanceReportRecords()
                            ->where('course_batch_id', $batch->id)
                            ->get()
                            ->unique('course_batch_session_id');
    
                        $presentCount = $attendanceRecords->whereIn('status', [1, 2, 3])->count();
                        $absentCount = $attendanceRecords->where('status', 0)->count();
    
                        $attendancePercentage = $totalSessionsCount > 0 ? ($presentCount / $totalSessionsCount) * 100 : 0;
    
                        $invoiceQuery = Invoice::where('trainee_id', $trainee->id);
    
                        if ($companyId) {
                            $invoiceQuery->where('company_id', $companyId);
                        }
    
                        $invoice = $invoiceQuery->where(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
                            $query->whereBetween('from_date', [$startOfTargetMonth, $endOfTargetMonth])
                                ->orWhereBetween('to_date', [$startOfTargetMonth, $endOfTargetMonth])
                                ->orWhere(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
                                    $query->where('from_date', '<=', $startOfTargetMonth)
                                        ->where('to_date', '>=', $endOfTargetMonth);
                                });
                        })->first();
    
                        $invoiceStatus = $invoice ? $invoice->status_formatted : 'لا توجد فاتورة';
                        $paidDate = $invoice ? $invoice->paid_at : '';
                        $invoiceFromDate = $invoice ? $invoice->from_date : '';
                        $invoiceToDate = $invoice ? $invoice->to_date : '';
    
                        if (isset($trainee->name)) {
                            $results[] = [
                                'paid_date' => $paidDate,
                                'invoice_to_date' => $invoiceToDate,
                                'invoice_from_date' => $invoiceFromDate,
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
            }
        }
    
        $results = collect($results)->sortByDesc(function ($trainee) {
            $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
            return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
        })->values()->toArray();
    
        return Excel::download(new TraineeAttendanceExportByGroup($results), 'trainee_attendance_by_course.xlsx');
    }
  



    

}

    


