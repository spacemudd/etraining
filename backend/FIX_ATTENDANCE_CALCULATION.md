# إصلاح حساب الحضور للمتدربين المحذوفين

## التعديلات المطلوبة

### 1. إضافة دالة في CompanyAttendanceReportService.php
تم إضافة الدالة التالية:

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

### 2. تعديل ملفات العرض

#### في جميع ملفات العرض، استبدل:
```php
@if (isset($record->is_resignation) && $record->is_resignation)
    @php
        $workDaysCount = 0;
        for($i=0;$i<count($days);$i++) {
            if (!$days[$i]['vacation_day']) {
                if ($record->start_date) {
                    if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                        $workDaysCount++;
                    }
                } else {
                    $workDaysCount++;
                }
            }
        }
    @endphp
    {{ $workDaysCount }}
@else
```

#### بـ:
```php
@if (isset($record->is_resignation) && $record->is_resignation)
    @php
        $attendance = \App\Services\CompanyAttendanceReportService::calculateAttendanceForDeletedTrainee($record, $days);
    @endphp
    {{ $attendance['work_days'] }}
@else
```

#### وفي حساب أيام الغياب، استبدل:
```php
@if (isset($record->is_resignation) && $record->is_resignation)
    0
@else
```

#### بـ:
```php
@if (isset($record->is_resignation) && $record->is_resignation)
    @php
        $attendance = \App\Services\CompanyAttendanceReportService::calculateAttendanceForDeletedTrainee($record, $days);
    @endphp
    {{ $attendance['absence_days'] }}
@else
```

### 3. الملفات التي تحتاج تعديل:
- `resources/views/pdf/company-attendance-report/special-company-simple.blade.php`
- `resources/views/pdf/company-attendance-report/one-table.blade.php`
- `resources/views/pdf/company-attendance-report/show.blade.php`
- `resources/views/pdf/company-attendance-report/individual-table.blade.php`
- `resources/views/pdf/company-attendance-report/special-company-individual-simple.blade.php`

## النتيجة المتوقعة
- عدد أيام العمل سيكون مطابقاً لعدد الأيقونات الفعلية في الجدول
- عدد أيام الغياب سيكون مطابقاً لعدد أيقونات الغياب الفعلية
- الحساب يتم من الباك اند مما يضمن الدقة والاتساق
