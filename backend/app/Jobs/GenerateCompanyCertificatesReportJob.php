<?php

namespace App\Jobs;

use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\Invoice;
use App\Exports\TraineeAttendanceExportByGroup;
use App\Models\JobTracker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class GenerateCompanyCertificatesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200; // ساعتان
    public $tries = 1;
    public $memory = 512; // MB

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

    public function handle()
    {
        try {
            Log::info('GenerateCompanyCertificatesReportJob started', [
                'tracker_id' => $this->tracker->id,
                'user_id' => $this->tracker->user_id,
                'timestamp' => now()->toDateTimeString(),
            ]);

            $this->tracker->update(['started_at' => now()]);

            $requestData = $this->tracker->metadata;
            $selectedCourseIds = array_column($requestData['courseId'], 'id');
            $selectedCourses = Course::whereIn('id', $selectedCourseIds)->get();
            $courseNames = $selectedCourses->pluck('name_ar')->unique()->toArray();
            $courses = Course::whereIn('name_ar', $courseNames)->get();

            $results = [];
            ini_set('memory_limit', '1024M');
            // set_time_limit لا يعمل مع queue jobs، يتم التحكم بالوقت عبر $timeout property

            $companyIds = array_column($requestData['companyId'], 'id');
            $companyName = '';

            foreach ($courses as $course) {
                $batches = $course->batches;

                foreach ($batches as $batch) {
                    Log::info('Batch ID: ' . $batch->id);
                    if (!$batch->trainee_group) {
                        Log::warning("Batch {$batch->id} has no trainee group.");
                        continue;
                    }
                    $courseEndDate = Carbon::parse($batch->ends_at);
                    $startOfMonth = $courseEndDate->copy()->startOfMonth();
                    $daysDifference = $courseEndDate->diffInDays($startOfMonth);

                    if ($daysDifference >= 15) {
                        $targetMonth = $courseEndDate->month;
                        $targetYear = $courseEndDate->year;
                    } else {
                        $targetMonth = $courseEndDate->subMonth()->month;
                        $targetYear = $courseEndDate->year;
                    }

                    $startOfTargetMonth = Carbon::createFromDate($targetYear, $targetMonth, 1)->startOfMonth();
                    $endOfTargetMonth = Carbon::createFromDate($targetYear, $targetMonth, 1)->endOfMonth();

                    $totalSessionsCount = $batch->course_batch_sessions()->count();

                    $traineesQuery = $batch->trainee_group->traineesWithTrashed();

                    if (!empty($companyIds)) {
                        $traineesQuery->whereIn('company_id', $companyIds);
                        $companies = Company::whereIn('id', $companyIds)->get();
                        $companyName = $companies->pluck('name_ar')->implode(', ');
                    }

                    $traineesQueryClone = clone $traineesQuery;
                    Log::info('Trainees count before chunk: ' . $traineesQueryClone->with(['user', 'company'])->count());

                    // تحسين الاستعلامات باستخدام eager loading
                    $traineesQuery->with(['user', 'company'])->chunk(100, function ($traineesChunk) use (
                        &$results,
                        $batch,
                        $totalSessionsCount,
                        $startOfTargetMonth,
                        $endOfTargetMonth,
                        $course,
                        $companyName,
                        $companyIds
                    ) {
                        foreach ($traineesChunk as $trainee) {
                            $attendanceRecords = $trainee->attendanceReportRecords()
                                ->where('course_batch_id', $batch->id)
                                ->get()
                                ->unique('course_batch_session_id');

                            Log::info('Attendance records count: ' . $attendanceRecords->count());

                            $presentCount = $attendanceRecords->whereIn('status', [1, 2, 3])->count();
                            $absentCount = $attendanceRecords->where('status', 0)->count();

                            $attendancePercentage = $totalSessionsCount > 0 ? ($presentCount / $totalSessionsCount) * 100 : 0;

                            $invoiceQuery = Invoice::where('trainee_id', $trainee->id);

                            if (!empty($companyIds)) {
                                $invoiceQuery->whereIn('company_id', $companyIds);
                            }

                            $invoice = $invoiceQuery->where(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
                                $query->whereBetween('from_date', [$startOfTargetMonth, $endOfTargetMonth])
                                    ->orWhereBetween('to_date', [$startOfTargetMonth, $endOfTargetMonth])
                                    ->orWhere(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
                                        $query->where('from_date', '<=', $startOfTargetMonth)
                                            ->where('to_date', '>=', $endOfTargetMonth);
                                    });
                            })->first();

                            $invoiceStatus = $invoice ? $invoice->status_formatted : 'لا توجد فاتورة';
                            $paidDate = $invoice ? $invoice->paid_at : '';
                            $invoiceFromDate = $invoice ? $invoice->from_date : '';
                            $invoiceToDate = $invoice ? $invoice->to_date : '';

                            $traineeCompanyName = $trainee->company ? $trainee->company->name_ar : 'غير مربوط بشركة';

                            if ($trainee->name) {
                                Log::info('Adding trainee to report: ' . $trainee->name);
                                $results[] = [
                                    'paid_date' => $paidDate,
                                    'invoice_to_date' => $invoiceToDate,
                                    'invoice_from_date' => $invoiceFromDate,
                                    'invoice_status' => $invoiceStatus,
                                    'attendance_percentage' => round($attendancePercentage, 2) . ' %',
                                    'present_count' => $presentCount,
                                    'absent_count' => $absentCount,
                                    'course_name' => $course->name_ar,
                                    'company_name' => $traineeCompanyName,
                                    'email' => $trainee->email,
                                    'phone' => $trainee->phone,
                                    'identity_number' => $trainee->identity_number,
                                    'trainee_name' => $trainee->name,
                                    'deleted_at' => $trainee->deleted_at,
                                    'last_login_at' => optional($trainee->user)->last_login_at,
                                ];
                            } else {
                                Log::info('Skipped trainee, name missing or invoice not matched: ' . json_encode([
                                    'trainee_id' => $trainee->id,
                                    'name' => $trainee->name,
                                    'invoice' => $invoice ? $invoice->id : null,
                                ]));
                            }
                        }
                    });
                }
            }

            $results = collect($results)->sortByDesc(function ($trainee) {
                $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
                return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
            })->values()->toArray();

            $fileName = 'attendance_report_' . now()->timestamp . '.xlsx';
            $localFilePath = storage_path('app/' . $fileName);

            Excel::store(new TraineeAttendanceExportByGroup($results), $fileName, 'local');

            $this->tracker->addMedia($localFilePath)
                ->withAttributes([
                    'team_id' => $this->tracker->team_id,
                ])->toMediaCollection('excel');

            $this->tracker->update(['finished_at' => now()]);

            Log::info('GenerateCompanyCertificatesReportJob completed', [
                'timestamp' => now()->toDateTimeString(),
                'tracker_id' => $this->tracker->id,
                'user_id' => $this->tracker->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('GenerateCompanyCertificatesReportJob failed', [
                'tracker_id' => $this->tracker->id,
                'user_id' => $this->tracker->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e; // إعادة رمي الخطأ ليتم تسجيله كـ failed job
        }
    }

    public function failed(Throwable $exception)
    {
        Log::error('GenerateCompanyCertificatesReportJob permanently failed', [
            'tracker_id' => $this->tracker->id,
            'user_id' => $this->tracker->user_id,
            'error' => $exception->getMessage(),
        ]);

        $this->tracker->failure_reason = $exception->getMessage();
        $this->tracker->save();
    }
}
