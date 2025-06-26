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
use Illuminate\Support\Facades\Cache;
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

        try {
            // Get filters from metadata
            $filters = $this->tracker->metadata;
            
            // Build query
            $query = Trainee::with(['company', 'educational_level', 'city', 'marital_status', 'deleted_by'])
                ->withTrashed(); // Include deleted trainees

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

            // Create Excel file
            $fileName = $this->generateExcel($query, $totalCount);

            $executionTime = microtime(true) - $startTime;
            Log::info('[TraineesReportJob] Report generated in ' . $executionTime . ' seconds');

            // Save to media collection
            $this->tracker->addMedia(storage_path('app/'.$fileName))
                ->withAttributes([
                    'team_id' => $this->tracker->team_id,
                ])->toMediaCollection('excel');

            $this->tracker->update(['finished_at' => now()]);
            
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
    private function generateExcel($query, $totalCount)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'الاسم',
            'البريد الإلكتروني', 
            'رقم الهوية',
            'الهاتف',
            'الهاتف الإضافي',
            'تاريخ الميلاد',
            'العمر',
            'الشركة',
            'المستوى التعليمي',
            'المدينة',
            'الحالة الاجتماعية',
            'عدد الأطفال',
            'الحالة',
            'يملك رقم الهاتف',
            'له فواتير',
            'علامة الحذف',
            'تاريخ الحذف',
            'محذوف بواسطة',
            'تاريخ الإنشاء'
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
        $sheet->getStyle('A1:S1')->applyFromArray($headerStyle);

        // Process data in chunks for memory efficiency
        $chunkSize = 100;
        $processed = 0;
        $row = 2;

        $query->chunk($chunkSize, function ($trainees) use ($sheet, &$row, &$processed, $totalCount) {
            foreach ($trainees as $trainee) {
                $age = $trainee->birthday ? Carbon::parse($trainee->birthday)->age : 'N/A';
                $hasInvoices = $trainee->invoices()->exists() ? 'نعم' : 'لا';
                $phoneOwnership = $this->getPhoneOwnershipStatus($trainee);
                $status = $this->getTraineeStatus($trainee);
                
                $data = [
                    $trainee->name,
                    $trainee->email,
                    $trainee->identity_number,
                    $trainee->phone,
                    $trainee->phone_additional,
                    $trainee->birthday ? Carbon::parse($trainee->birthday)->format('Y-m-d') : '',
                    $age,
                    optional($trainee->company)->name_ar,
                    optional($trainee->educational_level)->name_ar,
                    optional($trainee->city)->name_ar,
                    optional($trainee->marital_status)->name_ar,
                    $trainee->children_count,
                    $status,
                    $phoneOwnership,
                    $hasInvoices,
                    $trainee->deleted_remark,
                    $trainee->deleted_at ? $trainee->deleted_at->format('Y-m-d') : '',
                    optional($trainee->deleted_by)->name,
                    $trainee->created_at->format('Y-m-d')
                ];
                
                $sheet->fromArray($data, null, 'A' . $row);
                $row++;
                $processed++;
            }
            
            // Update progress (simulate for better UX)
            $progress = min(($processed / $totalCount) * 90, 90); // Keep at 90% until finished
            Cache::put("job_progress_{$this->tracker->id}", $progress, 3600);
        });

        // Auto-size columns
        foreach (range('A', 'S') as $column) {
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
     * Get phone ownership status text
     */
    private function getPhoneOwnershipStatus($trainee)
    {
        if (!$trainee->phone_ownership_verified_at) {
            return 'لم يتم التحقق';
        }

        if ($trainee->phone_is_owned === true) {
            return 'يملكه';
        } elseif ($trainee->phone_is_owned === false) {
            return 'لا يملكه';
        }

        return 'لم يتم التحقق';
    }

    /**
     * Get trainee status text
     */
    private function getTraineeStatus($trainee)
    {
        switch ($trainee->status) {
            case Trainee::STATUS_PENDING_UPLOADING_FILES:
                return 'ملف غير مكتمل';
            case Trainee::STATUS_PENDING_APPROVAL:
                return 'مرشح مدرب';
            case Trainee::STATUS_APPROVED:
                return 'موافق عليه';
            default:
                return 'غير محدد';
        }
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