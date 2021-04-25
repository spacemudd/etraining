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

use App\Exports\ContractsExport;
use App\Models\Back\Company;
use Maatwebsite\Excel\Facades\Excel;

class ContractsReportFactory
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
     * Language of the app.
     *
     *
     */
    private $language;

    /**
     * Create a new instance.
     *
     * @return \App\Reports\ContractsReportFactory
     */
    public static function new(): ContractsReportFactory
    {
        return new self();
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
     * @return \App\Reports\ContractsReportFactory
     * @throws \Throwable
     */
    public function setStartDate(\Carbon\Carbon $startDate): ContractsReportFactory
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
     * @return \App\Reports\ContractsReportFactory
     * @throws \Throwable
     */
    public function setEndDate(\Carbon\Carbon $endDate): ContractsReportFactory
    {
        throw_if($endDate->isBefore($this->startDate), 'End date cant be before start date');
        $this->endDate = $endDate;
        return $this;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getContracts()
    {
        $q = Company::whereHas('contracts', function($query) {
            $query->whereBetween('contract_starts_at', [$this->startDate->startOfDay(), $this->endDate->endOfDay()]);
        })->get();

        return $q;
    }

    public function getFileName()
    {
        return $this->startDate->format('Y-m-d')
            .'-ContractsReport-'.
            $this->endDate->format('Y-m-d')
            .'.xlsx';
    }

    /**
     * Saves excel to disk.
     *
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function toExcel()
    {
        Excel::store(new ContractsExport($this->getContracts()), $this->getFileName());
        return $this->getFileName();
    }
}
