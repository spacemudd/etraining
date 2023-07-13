<?php

namespace App\Http\Controllers\Back;

use App\Exports\CompanyAttendanceReportSendStatusExcel;
use App\Exports\CompanyAttendanceSheetExport;
use App\Http\Controllers\Controller;
use App\Mail\CompanyAttendanceReportMail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use App\Services\CompanyAttendanceReportService;
use App\Services\CompanyMigrationHelper;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use PDF;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyAttendanceReportController extends Controller
{
    public function index()
    {
        $this->authorize('submit-company-attendance-report');

        $reports = QueryBuilder::for(CompanyAttendanceReport::class)
            ->with('company')
            ->withCount('trainees')
            ->defaultSort('-created_at')
            ->allowedFilters(['company.name_ar', 'status', 'date_from'])
            ->allowedSorts(['number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Reports/CompanyAttendance/Index', [
            'reports' => $reports,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
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
        $report = CompanyAttendanceReport::create([
            'company_id' => $company->id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'to_emails' => implode(', ', [$company->email]),
            'cc_emails' => implode(', ', [auth()->user()->email]),
        ]);
        $report->trainees()->attach($company->trainees()->pluck('id'));
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
            ->with('trainees')
            ->findOrFail($id);

        return Inertia::render('Back/Reports/CompanyAttendance/Show', [
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
            'to_emails' => 'nullable|string',
            'cc_emails' => 'nullable|string',
            'company_id' => 'nullable|exists:companies,id',
            'period' => 'nullable',
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
                'to_emails' => $request->to_emails,
                'cc_emails' => $request->cc_emails,
            ]);
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

        //if ($report->company->is_ptc_net) {
        //    app()->make(CompanyMigrationHelper::class)->setMailgunConfig();
        //}

        Mail::to($report->to_emails ? explode(', ', $report->to_emails) : null)
            ->cc($report->cc_emails ? explode(', ', $report->cc_emails) : null)
            ->send(new CompanyAttendanceReportMail($report->id));
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
}
