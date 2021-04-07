<?php

namespace App\Exports;

use App\Models\Back\CourseBatchSession;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CourseSessionsAttendanceSummarySheetExport implements WithMultipleSheets
{
    use Exportable;

    public $courseBatchSessions;

    /**
     * CourseSessionsAttendanceSummarySheetExport constructor.
     *
     * @param $courseBatchSessions CourseBatchSession|\Illuminate\Support\Collection
     */
    public function __construct($courseBatchSessions)
    {
        $this->courseBatchSessions = $courseBatchSessions;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->courseBatchSessions as $session) {
            $sheets[] = new CourseBatchSessionAttendanceSheet($session->id);

        }

        $sheets[] = new CourseBatchSessionAttendanceSummarySheet($this->courseBatchSessions);

        return $sheets;
    }
}
