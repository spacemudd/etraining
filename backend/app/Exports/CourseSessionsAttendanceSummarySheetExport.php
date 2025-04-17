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

    public $companyId;

    /**
     * CourseSessionsAttendanceSummarySheetExport constructor.
     *
     * @param $courseBatchSessions CourseBatchSession|\Illuminate\Support\Collection
     * @param $startDate
     * @param $endDate
     * @param null $companyId
     */
    public function __construct($courseBatchSessions, $startDate, $endDate, $companyId=null)
    {
        $this->courseBatchSessions = $courseBatchSessions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyId = $companyId;
    }

    public function sheets(): array
    {
        $sheets = [];

        $reportsIds = [];

        foreach ($this->courseBatchSessions as $session) {
            $sheets[] = new BulkCourseBatchSessionAttendanceSheet($session->id, $this->companyId);
            $reportsIds[] = optional($session->attendance_report)->id;
        }

        $sheets[] = new CourseBatchSessionAttendanceSummarySheet($this->courseBatchSessions, $reportsIds, $this->startDate, $this->endDate, $this->companyId);

        return $sheets;
    }
}
