<?php

namespace App\Jobs;

use App\Models\JobTracker;
use App\Models\User;
use App\Notifications\ReportReadyNotification;
use App\Reports\BulkCourseAttendanceReportFactory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class BulkCourseAttendanceReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tracker;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\JobTracker $tracker
     */
    public function __construct(JobTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception|\Throwable
     */
    public function handle()
    {
        Log::info('[BulkCourseAttendanceReportJob] Started. Tracker ID: '.$this->tracker->id);

        $this->tracker->update(['started_at' => now()]);

        $fileName = BulkCourseAttendanceReportFactory::new()
            ->setStartDate(Carbon::parse($this->tracker->metadata['date_from'])->startOfDay())
            ->setEndDate(Carbon::parse($this->tracker->metadata['date_to'])->endOfDay())
            ->setCoursesIds($this->tracker->metadata['courses_ids'])
            ->setCompanyId($this->tracker->metadata['company_id'] ?? null)
            ->toExcel();

        $this->tracker->addMedia(storage_path('app/'.$fileName))
            ->withAttributes([
                'team_id' => $this->tracker->team_id,
            ])->toMediaCollection('excel');

        $this->tracker->update(['finished_at' => now()]);

        $user = User::find($this->tracker->user_id);
//        if ($user) {
            $temporaryUrl = $this->tracker->getFirstMedia('excel')->getTemporaryUrl(
                now()->addDays(3),
                config('filesystems.disks.s3.url').'/'.$this->tracker->getFirstMedia('excel')->getPath()
            );
            $user->notify(new ReportReadyNotification($temporaryUrl));
  //      }

        Log::info('[BulkCourseAttendanceReportJob] Completed. Tracker ID: '.$this->tracker->id);
    }

    public function failed(Throwable $e)
    {
        Log::info('[BulkCourseAttendanceReportJob] Failed. Tracker ID: '.$this->tracker->id);
        $this->tracker->failure_reason = $e->getMessage();
        $this->tracker->save();
    }
}
