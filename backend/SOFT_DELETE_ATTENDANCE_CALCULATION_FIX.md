# إصلاح منطق حساب الحضور والانصراف للمتدربين المحذوفين

## المشكلة
كان منطق حساب عدد أيام العمل والغياب للمتدربين المحذوفين (soft delete) يعتمد على `start_date` و `end_date` بدلاً من حساب الأيقونات الفعلية الموجودة في جدول الحضور. هذا كان يؤدي إلى:
- حساب خاطئ لعدد أيام العمل
- عدم مطابقة الأرقام مع الأيقونات المرئية في الجدول
- **مشكلة جديدة**: في الشهور السابقة، كان الحساب يجمع جميع الأيام بدلاً من الأيام المحددة في الفترة المطلوبة

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

    // Get the report date range from the first day
    $reportStartDate = $days[0]['date_carbon'] ?? \Carbon\Carbon::parse($days[0]['date']);
    $reportEndDate = end($days)['date_carbon'] ?? \Carbon\Carbon::parse(end($days)['date']);

    \Log::info('Calculating attendance for deleted trainee ' . $record->trainee->id . 
              ' - Report period: ' . $reportStartDate->format('Y-m-d') . ' to ' . $reportEndDate->format('Y-m-d') . 
              ' - Trainee period: ' . ($record->start_date ? $record->start_date->format('Y-m-d') : 'null') . 
              ' to ' . ($record->end_date ? $record->end_date->format('Y-m-d') : 'null'));

    foreach ($days as $day) {
        if ($day['vacation_day']) {
            continue; // Skip vacation days
        }

        $dayDate = $day['date_carbon'] ?? \Carbon\Carbon::parse($day['date']);

        // Check if this day should show an icon based on the same logic used in the view
        $shouldShowIcon = false;
        $isPresent = false;
        $isAbsent = false;

        if ($record->start_date) {
            // Check if the day is within the report period AND within the trainee's work period
            if ($dayDate->isBetween($record->start_date, $record->end_date) && 
                $dayDate->isBetween($reportStartDate, $reportEndDate)) {
                $shouldShowIcon = true;
                $isPresent = true;
            } else {
                if ($record->status === 'new_registration' && 
                    $dayDate->isBetween($reportStartDate, $reportEndDate)) {
                    $shouldShowIcon = true;
                    $isAbsent = true;
                }
            }
        } else {
            // If no start_date, only count days within the report period
            if ($dayDate->isBetween($reportStartDate, $reportEndDate)) {
                $shouldShowIcon = true;
                $isPresent = true;
            }
        }

        if ($shouldShowIcon) {
            if ($isPresent) {
                $workDaysCount++;
            } elseif ($isAbsent) {
                $absenceDaysCount++;
            }
        }
    }

    \Log::info('Result for trainee ' . $record->trainee->id . ': work_days=' . $workDaysCount . ', absence_days=' . $absenceDaysCount);

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

## التحسينات الجديدة

### 1. حساب الفترة المحددة
- الدالة الآن تأخذ في الاعتبار الفترة المحددة في التقرير
- لا تجمع الأيام خارج الفترة المطلوبة
- تحسب فقط الأيام التي تظهر في الجدول الفعلي

### 2. معالجة التواريخ
- تدعم كلاً من `date_carbon` و `date` في مصفوفة الأيام
- تتعامل مع الحالات التي قد لا تحتوي على `date_carbon`

### 3. Logging للتشخيص
- إضافة logging مفصل لتتبع عملية الحساب
- يساعد في تشخيص المشاكل المستقبلية

## النتيجة
- عدد أيام العمل للمتدربين المحذوفين أصبح يعتمد على عدد الأيقونات الفعلية في الجدول
- عدد أيام الغياب أصبح يحسب بناءً على الأيقونات الفعلية للمتدربين المحذوفين
- الأرقام أصبحت متطابقة مع الأيقونات المرئية في الجدول
- الحساب يتم من الباك اند مما يضمن الدقة والاتساق
- **تم إصلاح مشكلة الشهور السابقة**: الآن يحسب فقط الأيام في الفترة المحددة

## كيفية تحديد المتدربين المحذوفين
يتم تحديد المتدربين المحذوفين من خلال:
- وجود `$record->is_resignation = true`
- وجود `$record->trainee->deleted_at` (soft delete)

## ملاحظات مهمة
- التغييرات تؤثر فقط على المتدربين المحذوفين (soft delete)
- المتدربين العاديين يستمرون في استخدام المنطق القديم
- جميع ملفات العرض تم تحديثها لضمان الاتساق
- الحساب يتم من الباك اند مما يضمن الدقة
- تم إضافة logging للتشخيص والمراقبة
