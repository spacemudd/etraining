<?php

namespace App\Exports;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseSessionsAttendanceSummarySheetExport implements ShouldQueue, WithMultipleSheets
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
