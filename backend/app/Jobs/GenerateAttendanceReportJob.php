<?php

namespace App\Jobs;

use App\Models\AttendanceReportDueDates;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\Course;
use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class GenerateAttendanceReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 دقائق
    public $tries = 3;

    protected $courseName, $startDate, $endDate, $companyId;
    protected $reportId;

    public function __construct($courseName, $startDate, $endDate, $companyId = null, $reportId = null)
    {
        $this->courseName = $courseName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyId = $companyId;
        $this->reportId = $reportId;
    }

    public function handle()
    {
        $courses = Course::where('name_ar', $this->courseName)->pluck('id')->toArray();
    
        // تعديل: استخدام withTrashed() لتضمين المتدربات المحذوفات
        $trainees = Trainee::withTrashed()->whereIn('id', function ($query) use ($courses) {
            $query->select('trainee_id')
                ->from('attendance_report_records')
                ->whereIn('course_id', $courses)
                ->whereBetween('session_starts_at', [$this->startDate, $this->endDate]);
        });
    
        if ($this->companyId) {
            $trainees = $trainees->where('company_id', $this->companyId);
        }
    
        $trainees = $trainees->get();
    
        $data = $trainees->map(function ($trainee) use ($courses) {
            // البحث عن الاستقالة النشطة للمتدرب
            $active_resignation = $trainee->getActiveResignation($this->startDate, $this->endDate);
            
            // تحديد تاريخ النهاية بناءً على الاستقالة
            $effectiveEndDate = $active_resignation ? $active_resignation->resignation_date : $this->endDate;
            
            $records = $trainee->attendanceReportRecords()
                ->whereIn('course_id', $courses)
                ->whereDate('session_starts_at', '>=', $this->startDate)
                ->whereDate('session_starts_at', '<=', $effectiveEndDate)
                ->get();
    
            $presentCount = $records->whereIn('status', [1, 2, 3])->count();
            $absentCount = $records->where('status', 0)->count();
            
            // إضافة معلومات الاستقالة
            $resignationInfo = '';
            if ($active_resignation) {
                $resignationInfo = ' (استقالة بتاريخ: ' . $active_resignation->resignation_date->format('Y-m-d') . ')';
            }
            
            // إضافة علامة للمتدربات المحذوفات
            $deletedMark = $trainee->trashed() ? ' [محذوف]' : '';
    
            return [
                'اسم المتدرب' => $trainee->name . $deletedMark . $resignationInfo,
                'رقم الهوية' => $trainee->identity_number,
                'رقم الجوال' => $trainee->phone,
                'اسم الشركة' => optional($trainee->company)->name_ar ?? 'بدون شركة',
                'اسم الكورس' => $this->courseName,
                'تاريخ البداية' => $this->startDate,
                'تاريخ النهاية' => $effectiveEndDate->format('Y-m-d'),
                'عدد الحضور' => (int) $presentCount,
                'عدد الغياب' => (int) $absentCount,
                'تاريخ الاستقالة' => $active_resignation ? $active_resignation->resignation_date->format('Y-m-d') : '',
                'سبب الاستقالة' => $active_resignation ? $active_resignation->reason : '',
            ];
        });
    
        $filename = 'attendance_report_' . now()->format('Ymd_His') . '.xlsx';
        $filepath = 'reports/' . $filename;
    
        Excel::store(new class($data) implements FromCollection, WithHeadings {
            protected $data;
            public function __construct(Collection $data) {
                $this->data = $data;
            }
            public function collection() {
                return $this->data;
            }
            public function headings(): array {
                return [
                    'اسم المتدرب',
                    'رقم الهوية',
                    'رقم الجوال',
                    'اسم الشركة',
                    'اسم الكورس',
                    'تاريخ البداية',
                    'تاريخ النهاية',
                    'عدد الحضور',
                    'عدد الغياب',
                    'تاريخ الاستقالة',
                    'سبب الاستقالة',
                ];
            }
        }, $filepath, 'public');
    
        if ($this->reportId) {
            AttendanceReportDueDates::where('id', $this->reportId)->update([
                'filename' => $filename,
                'status' => 'ready',
            ]);
        }
    }


}
