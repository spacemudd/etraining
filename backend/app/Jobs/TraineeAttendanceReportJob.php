<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\JobTracker;
use App\Reports\TraineeAttendanceReportFactory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class TraineeAttendanceReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;

    public $tries = 3;

    public JobTracker $tracker;

    public function __construct(JobTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    public function handle(): void
    {
        Log::info('[TraineeAttendanceReportJob] Started. Tracker ID: '.$this->tracker->id);

        $this->tracker->update([
            'started_at' => now(),
            'total_records' => 0,
            'processed_records' => 0,
            'progress_percentage' => 0,
        ]);

        ini_set('memory_limit', '1024M');

        $fileName = TraineeAttendanceReportFactory::new()
            ->setStartDate(Carbon::parse($this->tracker->metadata['date_from'])->startOfDay())
            ->setEndDate(Carbon::parse($this->tracker->metadata['date_to'])->endOfDay())
            ->setProgressCallback(function (int $processed, int $total) {
                $progress = $total > 0 ? round(($processed / $total) * 100, 2) : 0;

                $this->tracker->update([
                    'total_records' => $total,
                    'processed_records' => $processed,
                    'progress_percentage' => $progress,
                ]);

                Log::info("[TraineeAttendanceReportJob] Progress: {$processed}/{$total} ({$progress}%)");
            })
            ->toExcel();

        $this->tracker->addMedia(storage_path('app/'.$fileName))
            ->withAttributes([
                'team_id' => $this->tracker->team_id,
            ])
            ->toMediaCollection('excel');

        $this->tracker->update([
            'finished_at' => now(),
            'progress_percentage' => 100,
        ]);

        Log::info('[TraineeAttendanceReportJob] Completed. Tracker ID: '.$this->tracker->id);
    }

    public function failed(Throwable $e): void
    {
        Log::error('[TraineeAttendanceReportJob] Failed. Tracker ID: '.$this->tracker->id.' | Error: '.$e->getMessage());

        $this->tracker->update([
            'failure_reason' => $e->getMessage(),
            'finished_at' => now(),
        ]);
    }
}
