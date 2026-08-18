<?php

namespace App\Exports;

use App\Models\Back\CourseBatch;
use App\Models\Back\Course;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CoursesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var Collection
     */
    private Collection $courses;

    public function __construct(Collection $courses)
    {
        $this->courses = $courses;
    }

    public static function fromQuery(): self
    {
        $courses = Course::with('instructor')->latest()->get();

        return new self($courses);
    }

    public function collection(): Collection
    {
        return $this->courses;
    }

    public function headings(): array
    {
        if (app()->getLocale() === 'ar') {
            return [
                'اسم الدورة',
                'كود الموافقة',
                'المدرب',
                'عدد الفصول',
                'حالة الموافقة',
                'تاريخ الدورة',
                'تاريخ بداية الدورة',
                'تاريخ نهاية الدورة',
            ];
        }

        return [
            'Course Name',
            'Approval Code',
            'Instructor',
            'Classroom Count',
            'Approval Status',
            'Course Date (Closest Batch)',
            'Course Start Date',
            'Course End Date',
        ];
    }

    public function map($course): array
    {
        $approvalStatus = '—';
        if ($course->is_pending_approval) {
            $approvalStatus = app()->getLocale() === 'ar' ? 'بانتظار الموافقة' : 'Pending approval';
        } elseif ($course->is_approved) {
            $approvalStatus = app()->getLocale() === 'ar' ? 'تمت الموافقة' : 'Approved';
        }

        $closestBatch = $course->closest_course_batch;
        if ($closestBatch === 'empty') {
            $closestBatch = app()->getLocale() === 'ar' ? 'غير محدد' : 'Not set';
        }

        $firstBatch = CourseBatch::where('course_id', $course->id)
            ->orderBy('starts_at', 'asc')
            ->first();

        $lastBatch = CourseBatch::where('course_id', $course->id)
            ->orderBy('ends_at', 'desc')
            ->first();

        $startDate = $firstBatch?->starts_at?->format('Y-m-d');
        $endDate = $lastBatch?->ends_at?->format('Y-m-d');

        if ($startDate === null) {
            $startDate = '—';
        }
        if ($endDate === null) {
            $endDate = '—';
        }

        return [
            $course->name_ar ?? $course->name_en,
            $course->approval_code,
            optional($course->instructor)->name,
            $course->classroom_count,
            $approvalStatus,
            $closestBatch,
            $startDate,
            $endDate,
        ];
    }
}

