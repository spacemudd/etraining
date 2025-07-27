<?php

namespace App\Jobs;

use App\Models\Back\Trainee;
use App\Models\JobTracker;
use App\Notifications\ReportCompletedNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class TraineesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 7200;

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
        Log::info('[TraineesReportJob] Started. Tracker ID: '.$this->tracker->id);
        $this->tracker->update(['started_at' => now()]);

        $startTime = microtime(true);
        $maxExecutionTime = 7000; // 7000 seconds (just under 2 hours)

        try {
            // Get filters from metadata
            $filters = $this->tracker->metadata;
            
            // Build query - don't load relationships for Excel export to save memory
            $query = Trainee::withTrashed(); // Include deleted trainees

            // Apply filters
            if (!empty($filters['age_under'])) {
                $dateThreshold = Carbon::now()->subYears($filters['age_under']);
                $query->where('birthday', '>', $dateThreshold);
            }

            if (!empty($filters['has_invoices'])) {
                if ($filters['has_invoices'] === 'yes') {
                    $query->whereHas('invoices');
                } else {
                    $query->whereDoesntHave('invoices');
                }
            }

            if (!empty($filters['assigned_to_company'])) {
                if ($filters['assigned_to_company'] === 'yes') {
                    $query->whereNotNull('company_id');
                } else {
                    $query->whereNull('company_id');
                }
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['phone_is_owned'])) {
                $isOwned = $filters['phone_is_owned'] === 'true';
                $query->where('phone_is_owned', $isOwned);
            }

            if (!empty($filters['educational_level_id'])) {
                $query->where('educational_level_id', $filters['educational_level_id']);
            }

            if (!empty($filters['deleted_mark'])) {
                $query->where('deleted_remark', $filters['deleted_mark']);
            }

            // Get total count for progress tracking
            $totalCount = $query->count();
            Log::info("[TraineesReportJob] Total trainees to process: {$totalCount}");

            // Update tracker with total count
            $this->tracker->update([
                'total_records' => $totalCount,
                'processed_records' => 0,
                'progress_percentage' => 0
            ]);

            // Create Excel file
            $fileName = $this->generateExcel($query, $totalCount, $startTime, $maxExecutionTime);

            $executionTime = microtime(true) - $startTime;
            Log::info('[TraineesReportJob] Report generated in ' . $executionTime . ' seconds');

            // Save to media collection
            $this->tracker->addMedia(storage_path('app/'.$fileName))
                ->withAttributes([
                    'team_id' => $this->tracker->team_id,
                ])->toMediaCollection('excel');

            $this->tracker->update([
                'finished_at' => now(),
                'progress_percentage' => 100
            ]);
            
            // Send notification
            if ($this->tracker->user_id && $user = \App\Models\User::find($this->tracker->user_id)) {
                $user->notify(new ReportCompletedNotification($this->tracker));
            }

            Log::info('[TraineesReportJob] Completed. Tracker ID: '.$this->tracker->id);
        } catch (Throwable $e) {
            $this->failed($e);
        }
    }

    /**
     * Generate Excel file from query
     */
    private function generateExcel($query, $totalCount, $startTime, $maxExecutionTime)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers - ALL database columns from trainees table
        $headers = [
            'id',
            'zoho_contract_id',
            'zoho_contract_status',
            'zoho_sign_date',
            'must_sign',
            'trainee_agreement_id',
            'team_id',
            'user_id',
            'name',
            'email',
            'identity_number',
            'phone',
            'phone_ownership_verified_at',
            'phone_is_owned',
            'phone_additional',
            'national_address',
            'birthday',
            'educational_level_id',
            'city_id',
            'marital_status_id',
            'children_count',
            'company_id',
            'entity_id',
            'deleted_at',
            'suspended_at',
            'gosi_deleted_at',
            'created_at',
            'updated_at',
            'instructor_id',
            'trainee_group_id',
            'status',
            'approved_by_id',
            'approved_at',
            'deleted_remark',
            'skip_uploading_id',
            'bill_from_date',
            'linked_date',
            'override_training_costs',
            'ignore_attendance',
            'dont_edit_notice',
            'suspended_by_id',
            'deleted_by_id',
            'posted_at',
            'trainee_message',
            'job_number',
            'english_name',
            'contract_signed_notification_sent'
        ];
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'E2E8F0']
            ]
        ];
        $sheet->getStyle('A1:AU1')->applyFromArray($headerStyle);

        // Process data in chunks for memory efficiency
        $chunkSize = 100;
        $processed = 0;
        $row = 2;

        $query->chunk($chunkSize, function ($trainees) use ($sheet, &$row, &$processed, $totalCount) {
            try {
                foreach ($trainees as $trainee) {
                    // Extract all database column values exactly as they are
                    $data = [
                        $trainee->id,
                        $trainee->zoho_contract_id,
                        $trainee->zoho_contract_status,
                        $trainee->zoho_sign_date,
                        $trainee->must_sign,
                        $trainee->trainee_agreement_id,
                        $trainee->team_id,
                        $trainee->user_id,
                        $trainee->name,
                        $trainee->email,
                        $trainee->identity_number,
                        $trainee->phone,
                        $trainee->phone_ownership_verified_at,
                        $trainee->phone_is_owned,
                        $trainee->phone_additional,
                        $trainee->national_address,
                        $trainee->birthday,
                        $trainee->educational_level_id,
                        $trainee->city_id,
                        $trainee->marital_status_id,
                        $trainee->children_count,
                        $trainee->company_id,
                        $trainee->entity_id,
                        $trainee->deleted_at,
                        $trainee->suspended_at,
                        $trainee->gosi_deleted_at,
                        $trainee->created_at,
                        $trainee->updated_at,
                        $trainee->instructor_id,
                        $trainee->trainee_group_id,
                        $trainee->status,
                        $trainee->approved_by_id,
                        $trainee->approved_at,
                        $trainee->deleted_remark,
                        $trainee->skip_uploading_id,
                        $trainee->bill_from_date,
                        $trainee->linked_date,
                        $trainee->override_training_costs,
                        $trainee->ignore_attendance,
                        $trainee->dont_edit_notice,
                        $trainee->suspended_by_id,
                        $trainee->deleted_by_id,
                        $trainee->posted_at,
                        $trainee->trainee_message,
                        $trainee->job_number,
                        $trainee->english_name,
                        $trainee->contract_signed_notification_sent
                    ];
                    
                    $sheet->fromArray($data, null, 'A' . $row);
                    $row++;
                    $processed++;
                }
                
                // Update real progress in database
                $progressPercentage = $totalCount > 0 ? round(($processed / $totalCount) * 100, 2) : 0;
                
                $this->tracker->update([
                    'processed_records' => $processed,
                    'progress_percentage' => $progressPercentage
                ]);
                
                Log::info("[TraineesReportJob] Progress: {$processed}/{$totalCount} ({$progressPercentage}%)");
                
                // Check for timeout
                if ((microtime(true) - $startTime) > $maxExecutionTime) {
                    throw new \Exception("Job execution time exceeded maximum allowed time of {$maxExecutionTime} seconds");
                }
            } catch (Throwable $e) {
                Log::error("[TraineesReportJob] Error processing chunk: " . $e->getMessage());
                Log::error($e->getTraceAsString());
                throw $e;
            }
        });

        // Auto-size columns for all 47 columns (A to AU)
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (range('AA', 'AU') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Save file
        $fileName = 'trainees_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $fileName;
    }



    /**
     * Handle job failure.
     *
     * @param Throwable $e
     */
    public function failed(Throwable $e)
    {
        Log::error('[TraineesReportJob] Failed. Tracker ID: ' . $this->tracker->id . ' | Error: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        $this->tracker->update([
            'failure_reason' => $e->getMessage(),
            'finished_at' => now(),
        ]);
    }
} 