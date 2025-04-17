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

namespace App\Reports;

use App\Exports\CourseSessionsAttendanceSummarySheetExport;
use App\Models\Back\CourseBatchSession;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BulkCourseAttendanceReportFactory
{
    /**
     * Courses ID.
     *
     * @var array
     */
    private $coursesIds;

    /**
     * The start date of the sessions.
     *
     * @var \Carbon\Carbon
     */
    private $startDate;

    /**
     * The end date of sessions.
     *
     * @var \Carbon\Carbon
     */
    private $endDate;

    /**
     * The company ID.
     *
     * @var string
     */
    private $companyId;

    /**
     * Create a new instance.
     *
     * @return BulkCourseAttendanceReportFactory
     */
    public static function new(): BulkCourseAttendanceReportFactory
    {
        return new self();
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    /**
     * @param string|null $companyId
     * @return BulkCourseAttendanceReportFactory
     */
    public function setCompanyId(string $companyId=null): BulkCourseAttendanceReportFactory
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return array
     */
    public function getCoursesIds(): array
    {
        return $this->coursesIds;
    }

    /**
     * @param array $coursesIds
     * @return BulkCourseAttendanceReportFactory
     */
    public function setCoursesIds(array $coursesIds): BulkCourseAttendanceReportFactory
    {
        $this->coursesIds = $coursesIds;
        return $this;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getStartDate(): \Carbon\Carbon
    {
        return $this->startDate;
    }

    /**
     * @param Carbon $startDate
     * @return BulkCourseAttendanceReportFactory
     * @throws \Throwable
     */
    public function setStartDate(\Carbon\Carbon $startDate): BulkCourseAttendanceReportFactory
    {
        throw_if(optional($this->endDate)->isBefore($startDate), 'Start date cant be before end date');
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getEndDate(): \Carbon\Carbon
    {
        return $this->endDate;
    }

    /**
     * @param Carbon $endDate
     * @return BulkCourseAttendanceReportFactory
     * @throws \Throwable
     */
    public function setEndDate(\Carbon\Carbon $endDate): BulkCourseAttendanceReportFactory
    {
        throw_if($endDate->isBefore($this->startDate), 'End date cant be before start date');
        $this->endDate = $endDate;
        return $this;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCourseSessions()
    {
        $q = CourseBatchSession::query();

        if ($this->coursesIds) {
            $q->whereIn('course_id', $this->coursesIds);
        }

        $q->orderBy('starts_at', 'desc')->whereBetween('starts_at', [$this->startDate, $this->endDate]);

        $q->whereHas('attendance_report');

        return $q->get();
    }

    /**
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->startDate->format('Y-m-d')
            .'-AttendanceReport-'.
            $this->endDate->format('Y-m-d')
            .'.xlsx';
    }

    /**
     *
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function toExcel()
    {
        Excel::store(new CourseSessionsAttendanceSummarySheetExport($this->getCourseSessions(), $this->startDate, $this->endDate, $this->companyId), $this->getFileName());
        return $this->getFileName();
    }
}
