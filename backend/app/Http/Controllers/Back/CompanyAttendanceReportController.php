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
use App\Models\Back\Trainee;
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
        
        // تعديل: تضمين المتدربات العاديات والمحذوفات الذين لهم استقالة فقط
        $all_trainees = Trainee::withTrashed()->where('company_id', $company->id)->get();
        $trainees_to_attach = [];
        
        foreach ($all_trainees as $trainee) {
            // البحث عن الاستقالة النشطة للمتدرب
            $active_resignation = $trainee->getActiveResignation($date_from, $date_to);
            
            // تضمين المتدرب إذا كان:
            // 1. غير محذوف، أو
            // 2. محذوف ولديه استقالة
            if (!$trainee->deleted_at || ($trainee->deleted_at && $active_resignation)) {
                // إضافة المتدرب مع معلومات الاستقالة والحذف
                $trainees_to_attach[$trainee->id] = [
                    'active' => true,
                    'start_date' => $date_from,
                    'end_date' => $active_resignation ? $active_resignation->resignation_date : $date_to,
                    'status' => $active_resignation ? 'resigned' : ($trainee->deleted_at ? 'deleted' : 'active'),
                    'comment' => $active_resignation ? 'استقالة بتاريخ: ' . $active_resignation->resignation_date->format('Y-m-d') : 
                                ($trainee->deleted_at ? 'محذوف بتاريخ: ' . $trainee->deleted_at->format('Y-m-d') : null),
                ];
            }
        }
        
        $report->trainees()->attach($trainees_to_attach);

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
            ->with(['trainees' => function($query) {
                $query->withTrashed();
            }])
            ->with('emails_to')
            ->with('emails_cc')
            ->findOrFail($id);

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
        $request->validate(['trainee_id' => 'required|exists:trainees,id']);
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
            ->where('trainee_id', $request->trainee_id)
            ->first();
        $record->active = true;
        $record->save();
        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    public function detach($id, Request $request)
    {
        $request->validate(['trainee_id' => 'required|exists:trainees,id']);
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
            ->where('trainee_id', $request->trainee_id)
            ->first();
        $record->active = false;
        $record->save();
        return redirect()->route('back.reports.company-attendance.show', $id);
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
        $pdf = CompanyAttendanceReportService::makePdf($id);

        return $pdf->inline();
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
        if ($report->trainees()->where('active', true)->count()) {
            $report->trainees()->update(['active' => false]);
        } else {
            $report->trainees()->update(['active' => true]);
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
            ->with('trainee', 'report')
            ->first();

        return Inertia::render('Back/Reports/CompanyAttendance/Individual', [
            'record' => $record,
        ]);
    }

    public function individualPdf($report_id, $trainee_id)
    {
        $pdf = CompanyAttendanceReportService::makeIndividualPdf($report_id, $trainee_id, request()->boolean('with_attendance_times'));
        return $pdf->inline();
    }

    public function individualEmail($report_id, $trainee_id)
    {
        $report = CompanyAttendanceReport::findOrFail($report_id);

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
            'trainee_id' => 'required|exists:trainees,id',
        ]);

        $report = CompanyAttendanceReport::findOrFail($report_id);

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
