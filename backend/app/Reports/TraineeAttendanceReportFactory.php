<?php

declare(strict_types=1);

namespace App\Reports;

use App\Services\TraineeAttendanceReportService;
use Carbon\Carbon;

class TraineeAttendanceReportFactory
{
    private Carbon $startDate;

    private Carbon $endDate;

    /** @var callable|null */
    private $progressCallback;

    public static function new(): self
    {
        return new self();
    }

    public function setStartDate(Carbon $startDate): self
    {
        throw_if(optional($this->endDate)->isBefore($startDate), 'Start date cant be before end date');
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(Carbon $endDate): self
    {
        throw_if(isset($this->startDate) && $endDate->isBefore($this->startDate), 'End date cant be before start date');
        $this->endDate = $endDate;

        return $this;
    }

    public function setProgressCallback(callable $callback): self
    {
        $this->progressCallback = $callback;

        return $this;
    }

    public function toExcel(): string
    {
        $service = new TraineeAttendanceReportService($this->startDate, $this->endDate);

        return $service->generateExcel($this->progressCallback);
    }
}
