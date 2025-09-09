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

            // Get all trainees including deleted ones and those with resignations
            $allTrainees = collect();
            
            // 1. Active trainees from the report (only those with checkbox selected)
            try {
                $activeTrainees = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $this->report->id)
                    ->where('active', true) // Only include trainees with checkbox selected
                    ->with(['trainee' => function($q) {
                        $q->withTrashed();
                    }])->get();
                    
                // Filter out records where trainee is null (in case of any data inconsistency)
                $activeTrainees = $activeTrainees->filter(function($record) {
                    return $record->trainee !== null;
                });
                $allTrainees = $allTrainees->merge($activeTrainees);
            } catch (\Exception $e) {
                \Log::error('Error getting active trainees for export report ' . $this->report->id . ': ' . $e->getMessage());
            }
            
            // 2. Get trainees with resignations AND deleted (soft deleted) - ONLY THESE SHOULD BE INCLUDED
            try {
                $resignationTrainees = $this->report->company->resignations()
                    ->whereIn('status', ['new', 'sent']) // Include both new and sent resignations
                    ->where('resignation_date', '>=', $this->report->date_from) // Resignation date should be within or after report period
                    ->with(['trainees' => function($q) {
                        $q->onlyTrashed(); // ONLY deleted trainees
                    }])
                    ->get()
                    ->flatMap(function($resignation) {
                        return $resignation->trainees->filter(function($trainee) {
                            return $trainee !== null;
                        })->map(function($trainee) use ($resignation) {
                            // Check if this trainee is in the attendance report and is active
                            $attendanceRecord = \App\Models\Back\CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $this->report->id)
                                ->where('trainee_id', $trainee->id)
                                ->where('active', true)
                                ->with(['trainee' => function($q) {
                                    $q->withTrashed();
                                }])
                                ->first();
                            
                            // Only include if the trainee is actively selected in the report
                            if (!$attendanceRecord) {
                                return null; // Skip this trainee
                            }
                            
                            // Use the original attendance record but add resignation info and reset dates
                            $attendanceRecord->is_resignation = true;
                            $attendanceRecord->resignation_date = $resignation->resignation_date;
                            
                            // For deleted trainees, we should not reset start_date and end_date to null
                            // Instead, we should keep the original values to maintain correct attendance calculation
                            // The view will handle the display logic for deleted trainees
                            
                            \Log::info('Using original attendance record for deleted trainee ' . $trainee->id . ' in export with original start_date and end_date');
                            
                            return $attendanceRecord;
                        })->filter(function($item) {
                            return $item !== null; // Remove null items
                        });
                    });
                
                \Log::info('Found ' . $resignationTrainees->count() . ' resignation trainees for export report ' . $this->report->id . ' (after filtering active ones)');
                $allTrainees = $allTrainees->merge($resignationTrainees);
            } catch (\Exception $e) {
                \Log::error('Error getting resignation trainees for export report ' . $this->report->id . ': ' . $e->getMessage());
            }
            
            // Remove duplicates based on trainee ID
            $uniqueTrainees = $allTrainees->unique(function($item) {
                return $item->trainee->id;
            });

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
