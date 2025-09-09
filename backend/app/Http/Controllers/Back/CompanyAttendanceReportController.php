<?php
namespace App\Http\Controllers\Back;

use App\Exports\CompanyAttendanceReportSendStatusExcel;
use App\Exports\CompanyAttendanceSheetExport;
use App\Http\Controllers\Controller;
use App\Mail\CompanyAttendanceIndividualReportMail;
use App\Mail\CompanyAttendanceReportMail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsEmail;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use App\Services\CompanyAttendanceReportService;
use App\Services\CompanyMigrationHelper;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PDF;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyAttendanceReportController extends Controller
{
    public function index()
    {
        $this->authorize('submit-company-attendance-report');

        $reports = QueryBuilder::for(CompanyAttendanceReport::class)
            ->with('company', 'created_by')
            ->withCount(['trainees' => function($q) {
                $q->where('active', true);
            }])
            ->defaultSort('-created_at')
            ->allowedFilters(['company.name_ar', 'status', 'date_from', 'number'])
            ->allowedSorts(['number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Reports/CompanyAttendance/Index', [
            'reports' => $reports,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'number' => __('words.number'),
                'company.name_ar' => __('words.company'),
                'date_from' => __('words.date'),
            ]);

            $table->addFilter('status', __('words.status'), [
                CompanyAttendanceReport::STATUS_REVIEW => __('words.review'),
                CompanyAttendanceReport::STATUS_APPROVED => __('words.approved'),
            ]);
        });
    }

    public function create()
    {
        return Inertia::render('Back/Reports/CompanyAttendance/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $date_from = Carbon::parse($request->period['startDate'])->setTimezone('Asia/Riyadh')->startOfDay();
        $date_to = Carbon::parse($request->period['endDate'])->setTimezone('Asia/Riyadh')->endOfDay();
        $company = Company::findOrFail($request->company_id);

        DB::beginTransaction();
        $previousReport = CompanyAttendanceReport::where('company_id', $company->id)->latest()->first();

        $report = CompanyAttendanceReport::create([
            'company_id' => $company->id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'with_logo' => false,
        ]);

        // Get all trainees including deleted ones and those with resignations
        $allTrainees = collect();
        
        // 1. Active trainees (not deleted)
        $activeTrainees = $company->trainees()->get();
        $allTrainees = $allTrainees->merge($activeTrainees);
        
        // 2. Trainees with resignations AND deleted (soft deleted) - ONLY THESE SHOULD BE INCLUDED
        $resignationTrainees = $company->resignations()
            ->whereIn('status', ['new', 'sent']) // Include both new and sent resignations
            ->where('resignation_date', '>=', $date_from) // Resignation date should be within or after report period
            ->with(['trainees' => function($q) {
                $q->onlyTrashed(); // ONLY deleted trainees
            }])
            ->get()
            ->flatMap(function($resignation) {
                return $resignation->trainees;
            });
        
        $allTrainees = $allTrainees->merge($resignationTrainees);
        
        // Remove duplicates and attach to report
        $uniqueTraineeIds = $allTrainees->unique('id')->pluck('id');
        
        // Prepare trainee data with start_date and end_date for resigned trainees
        $traineeData = [];
        foreach ($uniqueTraineeIds as $traineeId) {
            $trainee = $allTrainees->firstWhere('id', $traineeId);
            
            // Check if this trainee has a resignation
            $resignation = $company->resignations()
                ->whereIn('status', ['new', 'sent'])
                ->whereHas('trainees', function($q) use ($traineeId) {
                    $q->where('trainees.id', $traineeId); // Specify table name to avoid ambiguity
                })
                ->first();
            
            if ($resignation && $trainee->trashed()) {
                // This is a resigned and deleted trainee
                $traineeData[$traineeId] = [
                    'active' => true,
                    'start_date' => $date_from, // Start from report start date
                    'end_date' => Carbon::parse($resignation->resignation_date)->endOfDay(), // End at resignation date
                ];
            } else {
                // This is an active trainee - set start_date and end_date to null
                $traineeData[$traineeId] = [
                    'active' => true,
                    'start_date' => null,
                    'end_date' => null,
                ];
            }
        }
        
        $report->trainees()->attach($traineeData);

        // If there is an old report, use the previous emails. Otherwise, copy the company's emails.
        if ($previousReport) {
            $report->emails()->createMany($previousReport->emails()->get()->map(function ($email) {
                return [
                    'type' => $email->type,
                    'email' => $email->email,
                ];
            })->toArray());
        } else {
            if ($company->email) {
                $report->emails()->create(['type' => 'to', 'email' => $company->email]);
            }
            $report->emails()->create(['type' => 'cc', 'email' => auth()->user()->email]);
        }

        DB::commit();

        return redirect()->route('back.reports.company-attendance.show', $report->id);
    }

    /**
     * @param $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $report = CompanyAttendanceReport::with('company')->findOrFail($id);

        $report->status = 1;
        $report->approved_at = null;
        $report->approved_by_id = null;
        $report->save();

        return Inertia::render('Back/Reports/CompanyAttendance/Edit', [
            'report' => $report,
        ]);
    }

    public function show($id)
    {
        $report = CompanyAttendanceReport::with('company')
            ->with('approved_by')
            ->with('emails_to')
            ->with('emails_cc')
            ->findOrFail($id);

        // Get trainees including soft deleted ones from pivot table
        $trainees = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
            ->with(['trainee' => function($q) {
                $q->withTrashed(); // Include soft deleted trainees
            }])
            ->get()
            ->filter(function($record) {
                return $record->trainee !== null; // Filter out null trainees
            });

        // Add trainees to report
        $report->trainees = $trainees->map(function($record) {
            $trainee = $record->trainee;
            $trainee->pivot = $record; // Add pivot data
            return $trainee;
        });

        return Inertia::render('Back/Reports/CompanyAttendance/Show',[
            'report' => $report,
        ]);
    }

    public function destroy($id)
    {
        $r = CompanyAttendanceReport::findOrFail($id);
        abort_if($r->status === CompanyAttendanceReport::STATUS_APPROVED, 404);

        $r->delete();
        return redirect()->route('back.reports.company-attendance.index');
    }

    public function attach($id, Request $request)
    {
        try {
            $request->validate(['trainee_id' => 'required']);
            
            \Log::info('Attaching trainee ' . $request->trainee_id . ' to report ' . $id);
            
            // Check if trainee exists (including soft deleted ones)
            $traineeExists = \App\Models\Back\Trainee::withTrashed()->where('id', $request->trainee_id)->exists();
            if (!$traineeExists) {
                \Log::warning('Trainee ' . $request->trainee_id . ' not found (including soft deleted)');
                return back()->withErrors(['trainee_id' => 'المتدرب غير موجود']);
            }
            
            $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
                ->where('trainee_id', $request->trainee_id)
                ->first();
                
            if (!$record) {
                \Log::warning('Trainee ' . $request->trainee_id . ' not linked to report ' . $id);
                return back()->withErrors(['trainee_id' => 'المتدرب غير مرتبط بهذا التقرير']);
            }
            
            \Log::info('Found record for trainee ' . $request->trainee_id . ' in report ' . $id . '. Current active status: ' . ($record->active ? 'true' : 'false'));
            
            $record->active = true;
            $record->save();
            
            \Log::info('Successfully attached trainee ' . $request->trainee_id . ' to report ' . $id);
            
            return redirect()->route('back.reports.company-attendance.show', $id);
        } catch (\Exception $e) {
            \Log::error('Error attaching trainee ' . $request->trainee_id . ' to report ' . $id . ': ' . $e->getMessage());
            return back()->withErrors(['trainee_id' => 'حدث خطأ في إضافة المتدرب: ' . $e->getMessage()]);
        }
    }

    public function detach($id, Request $request)
    {
        try {
            $request->validate(['trainee_id' => 'required']);
            
            \Log::info('Detaching trainee ' . $request->trainee_id . ' from report ' . $id);
            
            // Check if trainee exists (including soft deleted ones)
            $traineeExists = \App\Models\Back\Trainee::withTrashed()->where('id', $request->trainee_id)->exists();
            if (!$traineeExists) {
                \Log::warning('Trainee ' . $request->trainee_id . ' not found (including soft deleted)');
                return back()->withErrors(['trainee_id' => 'المتدرب غير موجود']);
            }
            
            $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
                ->where('trainee_id', $request->trainee_id)
                ->first();
                
            if (!$record) {
                \Log::warning('Trainee ' . $request->trainee_id . ' not linked to report ' . $id);
                return back()->withErrors(['trainee_id' => 'المتدرب غير مرتبط بهذا التقرير']);
            }
            
            \Log::info('Found record for trainee ' . $request->trainee_id . ' in report ' . $id . '. Current active status: ' . ($record->active ? 'true' : 'false'));
            
            $record->active = false;
            $record->save();
            
            \Log::info('Successfully detached trainee ' . $request->trainee_id . ' from report ' . $id);
            
            return redirect()->route('back.reports.company-attendance.show', $id);
        } catch (\Exception $e) {
            \Log::error('Error detaching trainee ' . $request->trainee_id . ' from report ' . $id . ': ' . $e->getMessage());
            return back()->withErrors(['trainee_id' => 'حدث خطأ في إزالة المتدرب: ' . $e->getMessage()]);
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'period' => 'nullable',
            'with_attendance_times' => 'nullable|boolean',
            'with_logo' => 'nullable|boolean',
        ]);

        $report = CompanyAttendanceReport::findOrFail($id);

        if ($request->has('period')) {
            $date_from = Carbon::parse($request->period['startDate'])->setTimezone('Asia/Riyadh')->startOfDay();
            $date_to = Carbon::parse($request->period['endDate'])->setTimezone('Asia/Riyadh')->endOfDay();
            $company = Company::findOrFail($request->company_id);
            $report->update([
                'company_id' => $company->id,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'with_attendance_times' => $request->with_attendance_times,
                'with_logo' => $request->with_logo,
            ]);
            $report->save();
        } else {
            $report->update($request->except('_token'));
        }

        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    public function preview($id)
    {
        try {
            $pdf = CompanyAttendanceReportService::makePdf($id);
            return $pdf->inline();
        } catch (\Exception $e) {
            \Log::error('Error in preview for report ' . $id . ': ' . $e->getMessage());
            // Instead of redirecting back, return a simple error response
            return response()->json([
                'error' => true,
                'message' => 'حدث خطأ في عرض التقرير: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        app()->make(CompanyAttendanceReportService::class)->approve($id);

        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    public function send($id)
    {
        $report = CompanyAttendanceReport::findOrFail($id);

        // make sure to remove any spaces from emails
        foreach ($report->emails as $email) {
            $email->update([
                'email' => Str::replace(' ', '', $email->email),
            ]);
        }

        Mail::to($report->emails_to()->pluck('email') ?: null)
            ->bcc($report->emails_cc()->pluck('email') ?: null)
            ->send(new CompanyAttendanceReportMail($report->id));
            \Log::info('Emails To:', $report->emails_to()->pluck('email')->toArray());
            \Log::info('Emails BCC:', $report->emails_cc()->pluck('email')->toArray());



        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    public function clone($id)
    {
        $clone = app()->make(CompanyAttendanceReportService::class)->clone($id);

        return redirect()->route('back.reports.company-attendance.show', $clone->id);
    }

    public function sendReport()
    {
        return Inertia::render('Back/Reports/CompanyAttendance/SendReport');
    }

    public function sendReportDownload(Request $request)
    {
        $request->validate([
            'date' => 'required',
        ]);

        $start = Carbon::parse($request->date, 'Asia/Riyadh')->startOfMonth()->startOfDay();
        $end = Carbon::parse($request->date, 'Asia/Riyadh')->endOfMonth()->endOfDay();

        $companies = Company::with(['company_attendance_reports' => function($q) use ($start, $end) {
            $q->whereBetween('date_to', [$start, $end])
                ->where('status', CompanyAttendanceReport::STATUS_APPROVED);
        }])->get();

        return Excel::download(new CompanyAttendanceReportSendStatusExcel($companies, $start, $end), now()->format('Y-m-d').'-company-attendance-report.xlsx');
    }

    public function toggleSelect($id)
    {
        $report = CompanyAttendanceReport::findOrFail($id);
        
        // Use the pivot table directly to avoid issues with soft deleted trainees
        $activeCount = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
            ->where('active', true)
            ->count();
            
        if ($activeCount > 0) {
            CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
                ->update(['active' => false]);
        } else {
            CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
                ->update(['active' => true]);
        }
        
        return redirect()->route('back.reports.company-attendance.show', $id);
    }
    public function excel($report_id)
    {
        $report = CompanyAttendanceReport::findOrFail($report_id);
        $courseName = $report->company->name_ar;
        $sessionDate = $report->date_from->format('Y-m-d');
        return Excel::download(new CompanyAttendanceSheetExport($report), $courseName.'-'.$sessionDate.'-.xlsx');
    }

    public function individual($report_id, $trainee_id)
    {
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $trainee_id)
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }, 'report'])
            ->first();

        if (!$record) {
            return back()->withErrors(['message' => 'السجل غير موجود']);
        }

        return Inertia::render('Back/Reports/CompanyAttendance/Individual', [
            'record' => $record,
        ]);
    }

    public function individualPdf($report_id, $trainee_id)
    {
        // Check if record exists before generating PDF
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $trainee_id)
            ->first();
            
        if (!$record) {
            abort(404, 'السجل غير موجود');
        }
        
        $pdf = CompanyAttendanceReportService::makeIndividualPdf($report_id, $trainee_id, request()->boolean('with_attendance_times'));
        return $pdf->inline();
    }

    public function individualEmail($report_id, $trainee_id)
    {
        $report = CompanyAttendanceReport::findOrFail($report_id);
        
        // Check if record exists before sending email
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $trainee_id)
            ->first();
            
        if (!$record) {
            return back()->withErrors(['message' => 'السجل غير موجود']);
        }

        Mail::to($report->emails_to()->pluck('email') ?: null)
            ->bcc($report->emails_cc()->pluck('email') ?: null)
            ->send(new CompanyAttendanceIndividualReportMail($report->id, $trainee_id, request()->boolean('with_attendance_times')));

        return redirect()->route('back.reports.company-attendance.show', $report->id);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return void
     */
    public function addEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
            'type' => 'required|in:to,cc,bcc',
        ]);

        $company_attendance = CompanyAttendanceReport::findOrFail($id);
        if (!$company_attendance->emails()->where('type', $request->type)->where('email', $request->email)->exists()) {
            $company_attendance->emails()->create($request->except('_token'));
        }

        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return void
     */
    public function addEmailInBulk(Request $request, $id)
    {
        $request->validate([
            'email.*' => 'required|email:rfc,dns',
            'type' => 'required|in:to,cc,bcc',
        ]);

        $company_attendance = CompanyAttendanceReport::findOrFail($id);

        foreach ($request->email as $email) {
            if (!$company_attendance->emails()->where('type', $request->type)->where('email', $email)->exists()) {
                $company_attendance->emails()->create([
                    'email' => $email,
                    'type' => $request->type,
                ]);
            }
        }

        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    public function removeEmail($report_id, $id)
    {
        CompanyAttendanceReportsEmail::findOrFail($id)->delete();
        return redirect()->route('back.reports.company-attendance.show', $report_id);
    }

    public function addTrainee($report_id, Request $request)
    {
        $request->validate([
            'trainee_id' => 'required',
        ]);

        // Check if trainee exists (including soft deleted ones)
        $traineeExists = \App\Models\Back\Trainee::withTrashed()->where('id', $request->trainee_id)->exists();
        if (!$traineeExists) {
            return back()->withErrors(['trainee_id' => 'المتدرب غير موجود']);
        }

        $report = CompanyAttendanceReport::findOrFail($report_id);

        // Check if trainee is already attached to this report
        $existingRecord = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $request->trainee_id)
            ->first();
            
        if ($existingRecord) {
            return back()->withErrors(['trainee_id' => 'المتدرب مرتبط بالفعل بهذا التقرير']);
        }

        $report->trainees()->attach($request->trainee_id);

        return redirect()->route('back.reports.company-attendance.show', $report_id);
    }

    public function emailApprove($id)
{
    // Debugging
    dd("here");

    // Fetch the report by ID
    $report = CompanyAttendanceReport::findOrFail($id);
    
    // Update the approved_at timestamp
    $report->update(['approved_at' => now()]);
    
    // Redirect back to the report view with a success message
    return redirect()->route('back.reports.company-attendance.show', $id)->with('success', 'تم اعتماد التقرير بنجاح.');
}

}
