<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Exports;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordWarning;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseBatchSessionAttendanceSummarySheet implements FromView, WithEvents, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * @var
     */
    public $courseBatchSessions;

    /**
     * @var
     */
    public $reportIds;

    public $startDate;

    public $endDate;

    public function __construct($courseBatchSessions, array $reportIds, $startDate, $endDate)
    {
        $this->courseBatchSessions = $courseBatchSessions;
        $this->reportIds = $reportIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
        $attendanceRecords = AttendanceReportRecord::whereIn('attendance_report_id', $this->reportIds)
            ->whereHas('warnings')
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->groupBy('trainee_id')
            ->get();

        foreach ($attendanceRecords as $record) {
            $record->warnings_count = AttendanceReportRecordWarning::whereIn('attendance_report_id', $this->reportIds)
                ->where('trainee_id', $record->trainee_id)
                ->count();
        }

        return view('exports.attendingSummarySheet', [
            'courseName' => optional(optional(optional($this->courseBatchSessions)->first())->course)->name_ar,
            'courseBatchSessions' => $this->courseBatchSessions,
            'attendanceRecords' => $attendanceRecords,
        ]);
    }

    public function title(): string
    {
        return 'الملخص';
    }
}
