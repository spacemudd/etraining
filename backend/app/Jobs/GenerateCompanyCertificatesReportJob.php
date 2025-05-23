<?php

namespace App\Jobs;

use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\Invoice;
use App\Exports\TraineeAttendanceExportByGroup;
use App\Models\User;
use App\Notifications\ReportReadyNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class GenerateCompanyCertificatesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    protected $requestData;
    protected $userId;

    public function __construct(array $requestData, $userId)
    {
        $this->requestData = $requestData;
        $this->userId = $userId;
    }

    public function handle()
    {
        $course = Course::find($this->requestData['courseId']['id']);

        if (!$course) {
            return;
        }

        $courseName = $course->name_ar;
        $courses = Course::where('name_ar', $courseName)->get();

        $results = [];
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $companyId = $this->requestData['companyId']['id'] ?? null;
        $companyName = '';

        foreach ($courses as $course) {
            $batches = $course->batches;

            foreach ($batches as $batch) {
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

                if ($companyId) {
                    $traineesQuery->where('company_id', $companyId);
                    $company = Company::find($companyId);
                    if ($company) {
                        $companyName = $company->name_ar;
                    }
                }

                $traineesQuery->with('user')->with('company')->chunk(100, function ($traineesChunk) use (
                    &$results,
                    $batch,
                    $totalSessionsCount,
                    $startOfTargetMonth,
                    $endOfTargetMonth,
                    $courseName,
                    $companyName,
                    $companyId
                ) {
                    foreach ($traineesChunk as $trainee) {
                        $attendanceRecords = $trainee->attendanceReportRecords()
                            ->where('course_batch_id', $batch->id)
                            ->get()
                            ->unique('course_batch_session_id');

                        $presentCount = $attendanceRecords->whereIn('status', [1, 2, 3])->count();
                        $absentCount = $attendanceRecords->where('status', 0)->count();

                        $attendancePercentage = $totalSessionsCount > 0 ? ($presentCount / $totalSessionsCount) * 100 : 0;

                        $invoiceQuery = Invoice::where('trainee_id', $trainee->id);

                        if ($companyId) {
                            $invoiceQuery->where('company_id', $companyId);
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

                        if (isset($trainee->name)) {
                            $results[] = [
                                'paid_date' => $paidDate,
                                'invoice_to_date' => $invoiceToDate,
                                'invoice_from_date' => $invoiceFromDate,
                                'invoice_status' => $invoiceStatus,
                                'attendance_percentage' => round($attendancePercentage, 2) . ' %',
                                'present_count' => $presentCount,
                                'absent_count' => $absentCount,
                                'course_name' => $courseName,
                                'company_name' => $traineeCompanyName,
                                'email' => $trainee->email,
                                'phone' => $trainee->phone,
                                'identity_number' => $trainee->identity_number,
                                'trainee_name' => $trainee->name,
                                'deleted_at' => $trainee->deleted_at,
                                'last_login_at' => optional($trainee->user)->last_login_at,
                            ];
                        }
                    }
                });
            }
        }

        $results = collect($results)->sortByDesc(function ($trainee) {
            $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
            return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
        })->values()->toArray();

        $fileName = $companyName ? $companyName . '_attendance_report.xlsx' : 'trainee_attendance_by_course.xlsx';

        $filePath = 'reports/' . $fileName;
        Excel::store(new TraineeAttendanceExportByGroup($results), $filePath, 's3');

        $temporaryUrl = Storage::disk('s3')->temporaryUrl($filePath, now()->addMinutes(30));

        // Notify the user
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new ReportReadyNotification($temporaryUrl));
        }
    }
}
