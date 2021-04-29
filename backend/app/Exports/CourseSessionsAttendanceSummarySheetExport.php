<?php

namespace App\Exports;

use App\Models\Back\CourseBatchSession;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CourseSessionsAttendanceSummarySheetExport implements WithMultipleSheets
{
    use Exportable;

    public $courseBatchSessions;

    public $startDate;

    public $endDate;

    /**
     * CourseSessionsAttendanceSummarySheetExport constructor.
     *
     * @param $courseBatchSessions CourseBatchSession|\Illuminate\Support\Collection
     * @param $startDate
     * @param $endDate
     */
    public function __construct($courseBatchSessions, $startDate, $endDate)
    {
        $this->courseBatchSessions = $courseBatchSessions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        $reportsIds = [];

        foreach ($this->courseBatchSessions as $session) {
            $sheets[] = new CourseBatchSessionAttendanceSheet($session->id);
            $reportsIds[] = optional($session->attendance_report)->id;
        }

        $sheets[] = new CourseBatchSessionAttendanceSummarySheet($this->courseBatchSessions, $reportsIds, $this->startDate, $this->endDate);

        return $sheets;
    }
}
