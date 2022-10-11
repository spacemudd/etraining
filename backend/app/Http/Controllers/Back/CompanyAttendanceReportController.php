<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Mail\CompanyAttendanceReportMail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use App\Services\CompanyAttendanceReportService;
use Carbon\Carbon;
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

    public function show($id)
    {
        $report = CompanyAttendanceReport::with('company')
            ->with('trainees')
            ->findOrFail($id);

        return Inertia::render('Back/Reports/CompanyAttendance/Show', [
            'report' => $report,
        ]);
    }

    public function destroy($id)
    {
        $r = CompanyAttendanceReport::findOrFail($id);
        abort_if($r->status === CompanyAttendanceReport::STATUS_APPROVED);

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
        ]);

        $report = CompanyAttendanceReport::findOrFail($id);
        $report->update($request->except('_token'));

        return redirect()->route('back.reports.company-attendance.show', $id);
    }

    public function preview($id)
    {
        $pdf = CompanyAttendanceReportService::makePdf($id);

        return $pdf->inline();
    }

    public function approve($id)
    {
        $report = CompanyAttendanceReport::findOrFail($id);
        abort_if($report->status === CompanyAttendanceReport::STATUS_APPROVED);

        $report->status = CompanyAttendanceReport::STATUS_APPROVED;
        $report->approved_by_id = auth()->user()->id;
        $report->approved_at = now();
        $report->save();

        Mail::to(explode(', ', $report->to_emails))
            ->cc(explode(', ', $report->cc_emails))
            ->queue(new CompanyAttendanceReportMail($report->id));

        return redirect()->route('back.reports.company-attendance.show', $id);
    }
}
