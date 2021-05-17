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

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseBatchSessionAttendanceSheet implements FromView, WithEvents, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct($course_batch_session_id)
    {
        $this->course_batch_session_id = $course_batch_session_id;
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
            'A' => 15,
            'B' => 25,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 22,
            'H' => 22,

        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $report = AttendanceReport::where('course_batch_session_id', $this->course_batch_session_id)
            ->with(['course_batch_session' => function($q) {
                $q->with('course');
            }])
            ->latest()
            ->first();

        if (app()->getLocale() === 'ar') {
            $course_name = $report->course_batch_session->course->name_ar;
        } else {
            $course_name = $report->course_batch_session->course->name_en;
        }

        $attendances = AttendanceReportRecord::where('attendance_report_id', $report->id)
            ->whereHas('trainee', function($q) {
                $q->withTrashed();
            })
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }])
            ->whereIn('status', [AttendanceReportRecord::STATUS_PRESENT, AttendanceReportRecord::STATUS_LATE_TO_CLASS]);

        $absentees = AttendanceReportRecord::where('attendance_report_id', $report->id)
            ->whereHas('trainee', function($q) {
                $q->withTrashed();
            })
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }])
            ->whereIn('status', [AttendanceReportRecord::STATUS_ABSENT, AttendanceReportRecord::STATUS_ABSENT_WITH_EXCUSE]);

        $attendancesCount = $attendances->count();
        $absenteesCount = (clone $absentees)->where('status', AttendanceReportRecord::STATUS_ABSENT)->count();

        return view('exports.attendingSheet', [
            'course_batch' => $report->course_batch_session,
            'attendances' => $attendances->get(),
            'users_who_didnt_attend' => $absentees->get(),
            'course_name' => $course_name,
            'total_attendances' => $attendancesCount + $absenteesCount,
            'attendeesCount' => $attendancesCount,
            'absenteesCount' => $absenteesCount,
            'attendanceRate' => $attendancesCount+$absenteesCount ? round($attendancesCount / ($attendancesCount+$absenteesCount) * 100, 1) : '-',
        ]);

    }

    public function title(): string
    {
        return CourseBatchSession::find($this->course_batch_session_id)->starts_at->format('Y-m-d');
    }
}
