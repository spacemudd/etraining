<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Reports/CompanyAttendance/Index', [
            'reports' => $reports,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            //$table->addSearchRows([
            //    'number' => __('words.invoice-number'),
            //]);

            //$table->addFilter('status', __('words.status'), [
            //    Invoice::STATUS_UNPAID => __('words.unpaid'),
            //]);
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
        CompanyAttendanceReport::findOrFail($id)->delete();
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
        $report = CompanyAttendanceReport::findOrFail($id);

        return view('pdf.company-attendance-report.show', compact('report'));

        $pdf = PDF::setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait')
            ->setOption('encoding','utf-8')
            ->setOption('dpi', 300)
            ->setOption('image-dpi', 300)
            ->setOption('lowquality', false)
            ->setOption('no-background', false)
            ->setOption('enable-internal-links', true)
            ->setOption('enable-external-links', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('no-background', false)
            ->setOption('margin-left', 10)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 20)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)
            ->loadView('pdf.company-attendance-report.show', [
                'report' => $report,
            ]);

        return $pdf->inline();
    }
}
