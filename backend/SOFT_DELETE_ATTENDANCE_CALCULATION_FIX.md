# إصلاح منطق حساب الحضور والانصراف للمتدربين المحذوفين

## المشكلة
كان منطق حساب عدد أيام العمل والغياب للمتدربين المحذوفين (soft delete) يعتمد على `start_date` و `end_date` بدلاً من حساب الأيقونات الفعلية الموجودة في جدول الحضور. هذا كان يؤدي إلى:
- حساب خاطئ لعدد أيام العمل
- عدم مطابقة الأرقام مع الأيقونات المرئية في الجدول

## الحل المطبق

### 1. إنشاء دالة في الباك اند لحساب الأيقونات الفعلية
تم إضافة دالة جديدة في `app/Services/CompanyAttendanceReportService.php`:

```php
/**
 * Calculate work days and absence days for deleted trainees based on actual icons
 */
public static function calculateAttendanceForDeletedTrainee($record, $days)
{
    if (!isset($record->is_resignation) || !$record->is_resignation) {
        return [
            'work_days' => 0,
            'absence_days' => 0
        ];
    }

    $workDaysCount = 0;
    $absenceDaysCount = 0;

    foreach ($days as $day) {
        if ($day['vacation_day']) {
            continue; // Skip vacation days
        }

        // Check if this day should show an icon based on the same logic used in the view
        $shouldShowIcon = false;
        $isPresent = false;
        $isAbsent = false;

        if ($record->start_date) {
            if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                $shouldShowIcon = true;
                $isPresent = true;
            } else {
                if ($record->status === 'new_registration') {
                    $shouldShowIcon = true;
                    $isAbsent = true;
                }
            }
        } else {
            $shouldShowIcon = true;
            $isPresent = true;
        }

        if ($shouldShowIcon) {
            if ($isPresent) {
                $workDaysCount++;
            } elseif ($isAbsent) {
                $absenceDaysCount++;
            }
        }
    }

    return [
        'work_days' => $workDaysCount,
        'absence_days' => $absenceDaysCount
    ];
}
```

### 2. تعديل ملفات العرض لاستخدام الدالة الجديدة
تم تعديل جميع ملفات العرض لتستخدم الدالة الجديدة من الباك اند:

#### الملفات المعدلة:
- `resources/views/pdf/company-attendance-report/special-company-simple.blade.php`
- `resources/views/pdf/company-attendance-report/one-table.blade.php`
- `resources/views/pdf/company-attendance-report/show.blade.php`
- `resources/views/pdf/company-attendance-report/individual-table.blade.php`
- `resources/views/pdf/company-attendance-report/special-company-individual-simple.blade.php`

#### المنطق الجديد:
```php
@if (isset($record->is_resignation) && $record->is_resignation)
    @php
        $attendance = \App\Services\CompanyAttendanceReportService::calculateAttendanceForDeletedTrainee($record, $days);
    @endphp
    {{ $attendance['work_days'] }}  // عدد أيام العمل
    {{ $attendance['absence_days'] }}  // عدد أيام الغياب
@else
    // المنطق القديم للمتدربين العاديين
@endif
```

## النتيجة
- عدد أيام العمل للمتدربين المحذوفين أصبح يعتمد على عدد الأيقونات الفعلية في الجدول
- عدد أيام الغياب أصبح يحسب بناءً على الأيقونات الفعلية للمتدربين المحذوفين
- الأرقام أصبحت متطابقة مع الأيقونات المرئية في الجدول
- الحساب يتم من الباك اند مما يضمن الدقة والاتساق

## كيفية تحديد المتدربين المحذوفين
يتم تحديد المتدربين المحذوفين من خلال:
- وجود `$record->is_resignation = true`
- وجود `$record->trainee->deleted_at` (soft delete)

## ملاحظات مهمة
- التغييرات تؤثر فقط على المتدربين المحذوفين (soft delete)
- المتدربين العاديين يستمرون في استخدام المنطق القديم
- جميع ملفات العرض تم تحديثها لضمان الاتساق
- الحساب يتم من الباك اند مما يضمن الدقة
