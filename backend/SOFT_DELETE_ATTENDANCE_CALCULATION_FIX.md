# إصلاح منطق حساب الحضور والانصراف للمتدربين المحذوفين

## المشكلة
كان منطق حساب عدد أيام العمل والغياب للمتدربين المحذوفين (soft delete) يعتمد على `start_date` و `end_date` بدلاً من حساب الأيقونات الفعلية الموجودة في جدول الحضور. هذا كان يؤدي إلى:
- حساب خاطئ لعدد أيام العمل
- عدم مطابقة الأرقام مع الأيقونات المرئية في الجدول

## الحل المطبق

### 1. تعديل منطق حساب أيام العمل
تم تعديل جميع ملفات العرض لتستخدم منطق حساب جديد للمتدربين المحذوفين:

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
    // المنطق القديم للمتدربين العاديين
@endif
```

### 2. تعديل منطق حساب أيام الغياب
تم تعيين عدد أيام الغياب إلى صفر دائماً للمتدربين المحذوفين:

```php
@if (isset($record->is_resignation) && $record->is_resignation)
    0
@else
    // المنطق القديم للمتدربين العاديين
@endif
```

### 3. إصلاح ملف التصدير
تم تعديل `app/Exports/CompanyAttendanceSheetExport.php` لإزالة تعيين `start_date` و `end_date` إلى `null` للمتدربين المحذوفين.

## النتيجة
- عدد أيام العمل للمتدربين المحذوفين أصبح يعتمد على عدد الأيقونات الفعلية في الجدول
- عدد أيام الغياب أصبح صفر دائماً للمتدربين المحذوفين
- الأرقام أصبحت متطابقة مع الأيقونات المرئية في الجدول

## كيفية تحديد المتدربين المحذوفين
يتم تحديد المتدربين المحذوفين من خلال:
- وجود `$record->is_resignation = true`
- وجود `$record->trainee->deleted_at` (soft delete)

## ملاحظات مهمة
- التغييرات تؤثر فقط على المتدربين المحذوفين (soft delete)
- المتدربين العاديين يستمرون في استخدام المنطق القديم
- جميع ملفات العرض تم تحديثها لضمان الاتساق
