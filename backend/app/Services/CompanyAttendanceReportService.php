<?php

namespace App\Services;

use App\Models\Back\CompanyAttendanceReport;
use Carbon\Carbon;
use PDF;

class CompanyAttendanceReportService
{
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
            ->loadView('pdf.company-attendance-report.show', [
                'report' => $report,
                'active_trainees' => $report->getActiveTrainees(),
                'days' => $days,
            ]);

        return $pdf;
    }
}
