<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanyAttendanceReportSendStatusExcel implements FromCollection, WithMapping, WithHeadings
{
    public $companies;
    public $start;
    public $end;

    public function __construct($companies, $start, $end)
    {
        $this->companies = $companies;
        $this->start = $start;
        $this->end = $end;
    }

    public function headings(): array
    {
        return [
            __('words.company') .' - '.$this->start->format('Y-m'),
            __('words.reports-count'),
            __('words.trainees-count'),
        ];
    }

    public function map($company): array
    {
        return [
            $company->name_ar,
            $company->company_attendance_reports()->count() ?: '0',
            $this->reports_with_counts($company),
        ];
    }

    public function reports_with_counts($company)
    {
        $reports = [];
        foreach ($company->company_attendance_reports as $report) {
            $reports[] = $report->number . ' (' . $report->trainees()->count() . ')';
        }

        return implode(', ', $reports);
    }

    public function collection()
    {
        return $this->companies;
    }
}
