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
     * @param string|null $companyId
     * @return \App\Reports\CourseAttendanceReportFactory
     */
    public function setCompanyId(string $companyId=null): CourseAttendanceReportFactory
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
        ["731ea8d7-e884-4a5c-a6e7-0b54cafc4c6d","50e9fb28-84ec-4f0a-a124-c4623dca72e0","e7e44955-e525-4ecb-948d-b5424db650c0","a421e741-a53b-4073-9f6c-54302854d237","6a4b2d25-118b-423b-ac86-1f341ccfde32","8ef03b04-6220-4aab-958f-ec3ea150c32c","97903520-4fc0-4ba8-a7b4-e561f85c6bb6","332c29bf-c16d-405e-9f23-739624544230","7f4faec9-48b9-4be3-b8d9-9ff710567228","ff31cfa7-9527-400f-8b59-d97b2f056d16","4f53a321-5c65-4e0d-ac66-19896a6727b3","c12b13aa-e80d-435a-891b-278565eac071",];
    }
}
