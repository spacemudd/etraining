<?php

namespace App\Exports;

use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyAttendanceSheetExport implements FromView, WithEvents, WithStyles, WithColumnWidths
{
    public $report;

    public function __construct(CompanyAttendanceReport $report)
    {
        $this->report = $report;
    }

    public function registerEvents(): array
    {
        if (app()->getLocale() === 'ar') {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                },
            ];
        } else {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(false);
                },
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 12,
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 22,
            'G' => 22,

        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        try {
            if (app()->getLocale() === 'ar') {
                $company_name = $this->report->company->name_ar;
            } else {
                $company_name = $this->report->company->name_en;
            }

            // Get all active trainees from the report (only those with checkbox selected)
            $uniqueTrainees = collect();
            
            try {
                $activeTrainees = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $this->report->id)
                    ->where('active', true) // Only include trainees with checkbox selected
                    ->with(['trainee' => function($q) {
                        $q->withTrashed(); // Include soft deleted trainees
                    }])->get();
                    
                // Filter out records where trainee is null (in case of any data inconsistency)  
                $uniqueTrainees = $activeTrainees->filter(function($record) {
                    return $record->trainee !== null;
                });
                
                \Log::info('Found ' . $uniqueTrainees->count() . ' active selected trainees for export report ' . $this->report->id);
            } catch (\Exception $e) {
                \Log::error('Error getting trainees for export report ' . $this->report->id . ': ' . $e->getMessage());
            }

            $attendancesCount = $uniqueTrainees->count();

             return view('exports.company-attendance-sheet', [
                 'report' => $this->report,
                 'company_name' => $company_name,
                 'company_attendance' => $uniqueTrainees,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in CompanyAttendanceSheetExport view for report ' . $this->report->id . ': ' . $e->getMessage());
            // Return a basic view with error message
            return view('exports.company-attendance-sheet', [
                'report' => $this->report,
                'company_name' => 'Error',
                'company_attendance' => collect(),
            ]);
        }
    }
}
