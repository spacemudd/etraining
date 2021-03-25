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
use App\Models\Back\TraineeGroup;
use Maatwebsite\Excel\Facades\Excel;

class CourseAttendanceReportFactory
{
    /**
     * Course ID.
     *
     * @var string
     */
    private $courseId;

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
     * @return \App\Reports\CourseAttendanceReportFactory
     */
    public static function new(): CourseAttendanceReportFactory
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
     * @param string $companyId
     * @return \App\Reports\CourseAttendanceReportFactory
     */
    public function setCompanyId(string $companyId): CourseAttendanceReportFactory
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCourseId(): string
    {
        return $this->courseId;
    }

    /**
     * @param string $courseId
     * @return \App\Reports\CourseAttendanceReportFactory
     */
    public function setCourseId(string $courseId): CourseAttendanceReportFactory
    {
        $this->courseId = $courseId;
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
     * @param \Carbon\Carbon $startDate
     * @return \App\Reports\CourseAttendanceReportFactory
     * @throws \Throwable
     */
    public function setStartDate(\Carbon\Carbon $startDate): CourseAttendanceReportFactory
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
     * @param \Carbon\Carbon $endDate
     * @return \App\Reports\CourseAttendanceReportFactory
     * @throws \Throwable
     */
    public function setEndDate(\Carbon\Carbon $endDate): CourseAttendanceReportFactory
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

        if ($this->courseId) {
            $q->where('course_id', $this->courseId);
        }

        $q->whereBetween('starts_at', [$this->startDate, $this->endDate]);

        return $q->get();
    }

    public function getFileName()
    {
        return $this->startDate->format('Y-m-d')
            .'-AttendanceReport-'.
            $this->endDate->format('Y-m-d')
            .'.xlsx';
    }

    public function toExcel()
    {
        (new CourseSessionsAttendanceSummarySheetExport($this->getCourseSessions()))
            ->queue(storage_path('/reports/'.$this->getFileName()), 's3');
    }
}
