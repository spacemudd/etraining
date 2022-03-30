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
use App\Exports\InvoicesSheetExport;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\TraineeGroup;
use Maatwebsite\Excel\Facades\Excel;

class InvoicesReportFactory
{
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
     * Create a new instance.
     *
     * @return \App\Reports\InvoicesReportFactory
     */
    public static function new(): InvoicesReportFactory
    {
        return new self();
    }

    /**
     * @param string|null $companyId
     * @return \App\Reports\InvoicesReportFactory
     */
    public function setCompanyId(string $companyId=null): InvoicesReportFactory
    {
        $this->companyId = $companyId;
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
     * @return \App\Reports\InvoicesReportFactory
     * @throws \Throwable
     */
    public function setStartDate(\Carbon\Carbon $startDate): InvoicesReportFactory
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
     * @return \App\Reports\InvoicesReportFactory
     * @throws \Throwable
     */
    public function setEndDate(\Carbon\Carbon $endDate): InvoicesReportFactory
    {
        throw_if($endDate->isBefore($this->startDate), 'End date cant be before start date');
        $this->endDate = $endDate;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->startDate->format('Y-m-d')
            .'-InvoicesReport-'.
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
        Excel::store(new InvoicesSheetExport($this->startDate, $this->endDate, $this->companyId), $this->getFileName());
        return $this->getFileName();
    }
}
