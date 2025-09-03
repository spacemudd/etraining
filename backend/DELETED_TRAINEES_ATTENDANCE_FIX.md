# إصلاح مشكلة حساب أيام الحضور والغياب للمتدربين المحذوفين

## المشكلة
كانت هناك مشكلة في تقارير الحضور والانصراف للشركات حيث يظهر المتدربين المحذوفين ببيانات غريبة:
- عدد أيام الحضور لا يتطابق مع الفترة المحددة في التقرير
- عدد أيام الغياب ليس صفر كما يجب أن يكون

## السبب الجذري
المشكلة كانت في تعيين `start_date` و `end_date` للمتدربين المحذوفين في دالة `store()` في `CompanyAttendanceReportController.php`. كان يتم تعيين:
- `start_date` = تاريخ بداية التقرير
- `end_date` = تاريخ الاستقالة

هذا كان يسبب حساب خاطئ لأيام الحضور والغياب.

## الحل
تم إصلاح المشكلة بتعيين `start_date` و `end_date` إلى `null` للمتدربين المحذوفين، مما يجعلهم يعاملون مثل المتدربين النشطين العاديين.

## الملفات المعدلة

### 1. `app/Http/Controllers/Back/CompanyAttendanceReportController.php`
**الدالة**: `store()`
**التغيير**: تم تعديل منطق تعيين `start_date` و `end_date` للمتدربين المحذوفين

```php
// قبل الإصلاح
if ($resignation && $trainee->trashed()) {
    $traineeData[$traineeId] = [
        'active' => true,
        'start_date' => $date_from, // Start from report start date
        'end_date' => Carbon::parse($resignation->resignation_date)->endOfDay(), // End at resignation date
    ];
}

// بعد الإصلاح
if ($resignation && $trainee->trashed()) {
    $traineeData[$traineeId] = [
        'active' => true,
        'start_date' => null, // Set to null to ensure correct work days calculation
        'end_date' => null, // Set to null to ensure correct work days calculation
    ];
}
```

### 2. `app/Services/CompanyAttendanceReportService.php`
**الدوال**: `newReport()`, `clone()`
**الحالة**: كانت مسبقاً مسبقة الإصلاح وتستخدم المنطق الصحيح

## النتيجة المتوقعة
بعد هذا الإصلاح:
- المتدربين المحذوفين سيعرضون عدد أيام العمل الصحيح (28 يوم لشهر فبراير)
- عدد أيام الغياب سيكون صفر دائماً
- البيانات ستكون متسقة مع المتدربين النشطين

## الاختبار
يجب اختبار التقرير بعد الإصلاح للتأكد من:
1. عدد أيام العمل للمتدربين المحذوفين = 28 (لشهر فبراير)
2. عدد أيام الغياب للمتدربين المحذوفين = 0
3. جميع أيام الحضور تظهر علامة ✓
4. أيام العطلة (الجمعة والسبت) تظهر علامة X

## تاريخ الإصلاح
تم الإصلاح في: {{ date('Y-m-d H:i:s') }}
