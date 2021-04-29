<?php

namespace App\Exports;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceSheetExport implements FromView, WithEvents, WithStyles, WithColumnWidths
{
    public $report;

    public function __construct(AttendanceReport $report)
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
        if (app()->getLocale() === 'ar') {
            $course_name = $this->report->course_batch_session->course->name_ar;
        } else {
            $course_name = $this->report->course_batch_session->course->name_en;
        }

        $attendances = AttendanceReportRecord::where('attendance_report_id', $this->report->id)
            ->whereHas('trainee', function($q) {
                $q->withTrashed();
            })
            ->with(['trainee' => function($q) {
                $q->withTrashed();
            }])
            ->whereIn('status', [AttendanceReportRecord::STATUS_PRESENT, AttendanceReportRecord::STATUS_LATE_TO_CLASS]);

        $absentees = AttendanceReportRecord::where('attendance_report_id', $this->report->id)
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
             'course_name' => $course_name,
             'course_batch' => $this->report->course_batch_session,
             'attendances' => $attendances->get(),
             'users_who_didnt_attend' => $absentees->get(),
             'total_attendances' => $attendancesCount + $absenteesCount,
             'attendeesCount' => $attendancesCount,
             'absenteesCount' => $absenteesCount,
             'attendanceRate' => round($attendancesCount / ($attendancesCount+$absenteesCount) * 100, 1),
        ]);

    }
}
