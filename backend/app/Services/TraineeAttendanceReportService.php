<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\Course;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TraineeAttendanceReportService
{
    private const PRESENT_STATUSES = [
        AttendanceReportRecord::STATUS_ABSENT_WITH_EXCUSE,
        AttendanceReportRecord::STATUS_LATE_TO_CLASS,
        AttendanceReportRecord::STATUS_PRESENT,
    ];

    private const CHUNK_SIZE = 500;

    private Carbon $startDate;

    private Carbon $endDate;

    /** @var array<string, int> */
    private array $sessionsPerCourse = [];

    /** @var array<string, string> */
    private array $courseNames = [];

    /** @var array<string, array<string, array{present: int, absent: int, sessions: array<string, bool>}>> */
    private array $traineeCourseStats = [];

    /** @var array<string, array{present: int, absent: int, trainees: array<string, bool>}> */
    private array $courseSummary = [];

    /** @var array<string, Trainee> */
    private array $traineeCache = [];

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate->copy()->startOfDay();
        $this->endDate = $endDate->copy()->endOfDay();
    }

    public function generateExcel(?callable $progressCallback = null): string
    {
        $this->loadSessionsPerCourse();
        $this->aggregateAttendanceRecords($progressCallback);

        return $this->writeExcelFile();
    }

    private function sessionSubquery()
    {
        return CourseBatchSession::query()
            ->whereBetween('starts_at', [$this->startDate, $this->endDate])
            ->whereHas('attendance_report', function ($query) {
                $query->where('status', AttendanceReport::STATUS_SUBMITTED_REPORT);
            })
            ->select('id');
    }

    private function loadSessionsPerCourse(): void
    {
        $rows = CourseBatchSession::query()
            ->whereBetween('starts_at', [$this->startDate, $this->endDate])
            ->whereHas('attendance_report', function ($query) {
                $query->where('status', AttendanceReport::STATUS_SUBMITTED_REPORT);
            })
            ->select('course_id', DB::raw('COUNT(*) as session_count'))
            ->groupBy('course_id')
            ->get();

        foreach ($rows as $row) {
            $this->sessionsPerCourse[$row->course_id] = (int) $row->session_count;
        }

        if (!empty($this->sessionsPerCourse)) {
            $this->courseNames = Course::query()
                ->whereIn('id', array_keys($this->sessionsPerCourse))
                ->pluck('name_ar', 'id')
                ->all();
        }
    }

    private function aggregateAttendanceRecords(?callable $progressCallback = null): void
    {
        $totalRecords = AttendanceReportRecord::query()
            ->whereIn('course_batch_session_id', $this->sessionSubquery())
            ->whereBetween('session_starts_at', [$this->startDate, $this->endDate])
            ->count();

        $processed = 0;

        AttendanceReportRecord::query()
            ->whereIn('course_batch_session_id', $this->sessionSubquery())
            ->whereBetween('session_starts_at', [$this->startDate, $this->endDate])
            ->select([
                'id',
                'trainee_id',
                'course_id',
                'course_batch_session_id',
                'status',
            ])
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($records) use (&$processed, $totalRecords, $progressCallback) {
                $traineeIds = $records->pluck('trainee_id')->unique()->filter()->values()->all();
                $this->preloadTrainees($traineeIds);

                foreach ($records as $record) {
                    if (!$record->trainee_id || !$record->course_id || !$record->course_batch_session_id) {
                        continue;
                    }

                    $sessionKey = $record->course_batch_session_id;

                    if (isset($this->traineeCourseStats[$record->trainee_id][$record->course_id]['sessions'][$sessionKey])) {
                        continue;
                    }

                    $isPresent = in_array((int) $record->status, self::PRESENT_STATUSES, true);

                    if (!isset($this->traineeCourseStats[$record->trainee_id][$record->course_id])) {
                        $this->traineeCourseStats[$record->trainee_id][$record->course_id] = [
                            'present' => 0,
                            'absent' => 0,
                            'sessions' => [],
                        ];
                    }

                    $this->traineeCourseStats[$record->trainee_id][$record->course_id]['sessions'][$sessionKey] = true;

                    if ($isPresent) {
                        $this->traineeCourseStats[$record->trainee_id][$record->course_id]['present']++;
                    } else {
                        $this->traineeCourseStats[$record->trainee_id][$record->course_id]['absent']++;
                    }

                    if (!isset($this->courseSummary[$record->course_id])) {
                        $this->courseSummary[$record->course_id] = [
                            'present' => 0,
                            'absent' => 0,
                            'trainees' => [],
                        ];
                    }

                    if ($isPresent) {
                        $this->courseSummary[$record->course_id]['present']++;
                    } else {
                        $this->courseSummary[$record->course_id]['absent']++;
                    }

                    $this->courseSummary[$record->course_id]['trainees'][$record->trainee_id] = true;
                }

                $processed += $records->count();

                if ($progressCallback) {
                    $progressCallback($processed, $totalRecords);
                }
            });
    }

    /**
     * @param array<int|string> $traineeIds
     */
    private function preloadTrainees(array $traineeIds): void
    {
        $missingIds = array_diff($traineeIds, array_keys($this->traineeCache));

        if (empty($missingIds)) {
            return;
        }

        Trainee::withTrashed()
            ->with('company')
            ->whereIn('id', $missingIds)
            ->get()
            ->each(function (Trainee $trainee) {
                $this->traineeCache[$trainee->id] = $trainee;
            });
    }

    private function writeExcelFile(): string
    {
        $spreadsheet = new Spreadsheet();

        $this->writeDetailsSheet($spreadsheet->getActiveSheet());
        $this->writeSummarySheet($spreadsheet->createSheet());

        $fileName = $this->startDate->format('Y-m-d')
            .'-TraineeAttendanceReport-'
            .$this->endDate->format('Y-m-d')
            .'-'
            .now()->format('His')
            .'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/'.$fileName));

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $fileName;
    }

    private function writeDetailsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('تفاصيل الحضور');
        $sheet->setRightToLeft(true);

        $headers = [
            'اسم المتدرب',
            'رقم الهوية',
            'رقم الجوال',
            'البريد الإلكتروني',
            'الشركة',
            'الدورة',
            'عدد الجلسات',
            'عدد الحضور',
            'عدد الغياب',
            'نسبة الحضور',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $this->applyHeaderStyle($sheet, 'A1:J1');

        $row = 2;

        foreach ($this->traineeCourseStats as $traineeId => $courses) {
            $trainee = $this->traineeCache[$traineeId] ?? Trainee::withTrashed()->with('company')->find($traineeId);

            if (!$trainee || !$trainee->name) {
                continue;
            }

            foreach ($courses as $courseId => $stats) {
                if ($stats['present'] <= 0) {
                    continue;
                }

                $totalSessions = $this->sessionsPerCourse[$courseId] ?? 0;
                $percentage = $totalSessions > 0
                    ? round(($stats['present'] / $totalSessions) * 100, 2)
                    : 0;

                $sheet->fromArray([
                    $trainee->name,
                    $trainee->identity_number,
                    $trainee->phone,
                    $trainee->email,
                    optional($trainee->company)->name_ar ?? '',
                    $this->courseNames[$courseId] ?? '',
                    $totalSessions,
                    $stats['present'],
                    $stats['absent'],
                    $percentage.' %',
                ], null, 'A'.$row);

                $sheet->getStyle('A'.$row.':J'.$row)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $row++;
            }
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function writeSummarySheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('ملخص الدورات');
        $sheet->setRightToLeft(true);

        $headers = [
            'الدورة',
            'عدد الجلسات',
            'عدد المتدربات',
            'إجمالي الحضور',
            'إجمالي الغياب',
            'نسبة الحضور الإجمالية',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $this->applyHeaderStyle($sheet, 'A1:F1');

        $row = 2;

        ksort($this->courseSummary);

        foreach ($this->courseSummary as $courseId => $summary) {
            $totalSessions = $this->sessionsPerCourse[$courseId] ?? 0;
            $traineeCount = count($summary['trainees']);
            $totalPossible = $totalSessions * $traineeCount;
            $overallPercentage = $totalPossible > 0
                ? round(($summary['present'] / $totalPossible) * 100, 2)
                : 0;

            $sheet->fromArray([
                $this->courseNames[$courseId] ?? '',
                $totalSessions,
                $traineeCount,
                $summary['present'],
                $summary['absent'],
                $overallPercentage.' %',
            ], null, 'A'.$row);

            $sheet->getStyle('A'.$row.':F'.$row)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function applyHeaderStyle(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'E2E8F0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
