<?php

namespace App\Services;

use App\Mail\CompanyAttendanceReportMail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mail;
use PDF;
use Str;

class CompanyAttendanceReportService
{
    public function clone($id)
    {
        DB::beginTransaction();
        $original = CompanyAttendanceReport::findOrFail($id);
        $clone = $original->replicate(['approved_by_id', 'approved_at']);
        $clone->status = CompanyAttendanceReport::STATUS_REVIEW;
        $clone->date_from = $original->date_from->clone()->addMonth();
        if ($clone->date_from->daysInMonth < $original->date_from->daysInMonth) {
            $clone->date_to = $original->date_from
                ->clone()
                ->endOfMonth()
                ->addDay()
                ->endOfMonth();
        } else {
            $clone->date_to = $original->date_to->clone()->addMonth();
        }
        $clone->save();
        $clone = $clone->refresh();

        $clone->emails()->createMany($original->emails()->get()->map(function ($email) {
                return [
                    'type' => $email->type,
                    'email' => Str::replace(' ', '', $email->email),
                ];
            })->toArray());

        $original_trainees = $original->trainees->flatMap(function($trainee) {
            return [$trainee->id => ['active' => $trainee->pivot->active]];
        });

        $clone->trainees()->attach($original_trainees);

        DB::commit();

        return $clone;
    }

    public function approve($id)
    {
        $report = CompanyAttendanceReport::findOrFail($id);
        abort_if($report->status === CompanyAttendanceReport::STATUS_APPROVED, 404);

        $report->status = CompanyAttendanceReport::STATUS_APPROVED;
        $report->approved_by_id = auth()->user()->id ?? '7289ed33-0250-40dd-af4c-9f3e2b09eecb';
        $report->approved_at = now();
        $report->save();

        // make sure to remove any spaces from emails
        foreach ($report->emails as $email) {
            $email->update([
                'email' => Str::replace(' ', '', $email->email),
            ]);
        }

        Mail::to($report->emails_to()->pluck('email') ?: null)
            ->cc($report->emails_cc()->pluck('email') ?: null)
            ->send(new CompanyAttendanceReportMail($report->id));

        return $report;
    }

    static public function makePdf($id)
    {
        $report = CompanyAttendanceReport::findOrFail($id);

        $days = [];
        Carbon::setLocale('ar');
        $date_from = Carbon::parse($report->date_from);
        $date_to = Carbon::parse($report->date_to);
        $i = 0;
        $current_day = $date_from;

        for ($i; $current_day->isBefore($date_to); $i++) {
            $days[] = [
                'vacation_day' => in_array($current_day->getTranslatedDayName(), ['الجمعة', 'السبت']),
                'name' => $current_day->getTranslatedDayName(),
                'date' => $current_day->format('Y-m-d'),
                'date_carbon' => $current_day->copy(),
            ];
            $current_day = $current_day->addDay();
        }

        // To fix formatting issue on 2nd page when the table is split.
        $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';

        $pdf = PDF::setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape')
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
            ->setOption('margin-bottom', 30)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)
            ->setOption('footer-html', $report->with_logo ? resource_path('views/pdf/company-attendance-report/company-attendance-report-footer.html') : false)
            ->loadView($view, [
                'report' => $report,
                'active_trainees' => $report->getActiveTrainees(),
                'days' => $days,
            ]);

        return $pdf;
    }

    public function makeIndividualPdf($report_id, $trainee_id, bool $with_attendance_times)
    {
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $trainee_id)
            ->with('trainee', 'report')
            ->first();

        $days = [];
        Carbon::setLocale('ar');
        $date_from = Carbon::parse($record->report->date_from);
        $date_to = Carbon::parse($record->report->date_to);
        $i = 0;
        $current_day = $date_from;

        for ($i; $current_day->isBefore($date_to); $i++) {
            $days[] = [
                'vacation_day' => in_array($current_day->getTranslatedDayName(), ['الجمعة', 'السبت']),
                'name' => $current_day->getTranslatedDayName(),
                'date' => $current_day->format('Y-m-d'),
                'date_carbon' => $current_day->copy(),
            ];
            $current_day = $current_day->addDay();
        }

        $view = 'pdf.company-attendance-report.individual-table';

        $pdf = PDF::setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape')
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
            ->setOption('margin-bottom', 30)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)
            ->setOption('footer-html', resource_path('views/pdf/company-attendance-report/company-attendance-report-footer.html'))
            ->loadView($view, [
                'report' => $record->report,
                'active_trainees' => [$record],
                'days' => $days,
                'with_attendance_times' => $with_attendance_times,
            ]);

        return $pdf;
    }
}
