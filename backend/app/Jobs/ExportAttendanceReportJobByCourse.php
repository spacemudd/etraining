<?php

namespace App\Jobs;

use App\Exports\TraineeAttendanceExportByGroup;
use App\Models\Back\Course;
use App\Models\Back\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Excel;

class ExportAttendanceReportJobByCourse
{
    protected $courseId;

    public function __construct($courseId)
    {
        $this->courseId = $courseId;
    }

    public function handle()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $course = Course::find($this->courseId);
        $courseName = $course->name_ar;
        $courses = Course::where('name_ar', $courseName)->get();

        if ($courses->isEmpty()) {
            throw new \Exception("لا توجد كورسات بالاسم المحدد: $courseName");
        }

        $results = [];

        foreach ($courses as $course) {
            foreach ($course->batches as $courseBatch) {
                $courseEndDate = Carbon::parse($courseBatch->ends_at);
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

                $totalSessionsCount = $courseBatch->course_batch_sessions()->count();

                $courseBatch->trainee_group->trainees()->chunk(100, function ($traineesChunk) use (&$results, $courseBatch, $totalSessionsCount, $startOfTargetMonth, $endOfTargetMonth) {
                    foreach ($traineesChunk as $trainee) {
                        $attendanceRecords = $trainee->attendanceReportRecords()
                            ->where('course_batch_id', $courseBatch->id)
                            ->get()
                            ->unique('course_batch_session_id');

                        $presentCount = $attendanceRecords->whereIn('status', [1, 2, 3])->count();
                        $absentCount = $attendanceRecords->where('status', 0)->count();

                        $attendancePercentage = $totalSessionsCount > 0 ? ($presentCount / $totalSessionsCount) * 100 : 0;

                        $invoice = Invoice::where('trainee_id', $trainee->id)->where(function ($query) use ($startOfTargetMonth, $endOfTargetMonth) {
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

                        if (isset($trainee->name)) {
                            $results[] = [
                                'paid_date' => $paidDate,
                                'invoice_to_date' => $invoiceToDate,
                                'invoice_from_date' => $invoiceFromDate,
                                'invoice_status' => $invoiceStatus,
                                'attendance_percentage' => round($attendancePercentage, 2) . ' %',
                                'present_count' => $presentCount,
                                'absent_count' => $absentCount,
                                'email' => $trainee->email,
                                'phone' => $trainee->phone,
                                'identity_number' => $trainee->identity_number,
                                'trainee_name' => $trainee->name,
                            ];
                        }
                    }
                });
            }
        }

        return collect($results)->sortByDesc(function ($trainee) {
            $attendanceNumeric = floatval(str_replace(' %', '', $trainee['attendance_percentage']));
            return ($attendanceNumeric >= 50 && $trainee['invoice_status'] == 'مدفوع') ? 1 : 0;
        })->values()->toArray();
    }
}
