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

        $trainees = Trainee::whereIn('id', function ($query) use ($courses) {
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
            $records = $trainee->attendanceReportRecords()
                ->whereIn('course_id', $courses)
                ->whereBetween('session_starts_at', [$this->startDate, $this->endDate])
                ->get();

            $presentCount = $records->whereIn('status', [1, 2, 3])->count();
            $absentCount = $records->where('status', 0)->count();

            return [
                'اسم المتدرب' => $trainee->name,
                'رقم الهوية' => $trainee->identity_number,
                'رقم الجوال' => $trainee->phone,
                'اسم الشركة' => optional($trainee->company)->name_ar ?? 'بدون شركة',
                'اسم الكورس' => $this->courseName,
                'عدد الحضور' => $presentCount,
                'عدد الغياب' => $absentCount,
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
                    'عدد الحضور',
                    'عدد الغياب',
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
