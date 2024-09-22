<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Services\CompanyAttendanceReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class IsTraineeSuspendedCompanyAttendanceSheetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company-attendance-sheets:isTraineeSuspended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'isTraineeSuspended';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $reports = CompanyAttendanceReport::whereBetween('date_to', [Carbon::parse('2024-01-01')->startOfDay(), Carbon::parse('2024-01-31')->endOfDay()])->get();
        $this->info('count: '.$reports->count());
        foreach ($reports as $report) {
            $deletedTraineesBetween = Trainee::whereBetween('deleted_at',
                [Carbon::parse('2023-12-01')->startOfDay(), Carbon::parse('2023-12-31')->endOfDay()])
                ->withTrashed()
                ->get();

            $reportTrainees = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report->id)
                ->whereIn('trainee_id', $deletedTraineesBetween->pluck('id'))
                ->get();

            foreach ($reportTrainees as $reportTrainee) {
                if($reportTrainee)
                {
                    $this->info('trainee names: '.$reportTrainee->trainee->name);
                    continue;
                }
            }
        }
        return 0;
    }
}
