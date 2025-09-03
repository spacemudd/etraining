# إصلاح مشكلة حساب عدد أيام الحضور للمتدربين المحذوفين

## المشكلة
عند عمل تقرير حضور للشركة، إذا كان هناك متدربين محذوفين (مستقيلين) في التقرير، يظهر عدد أيام الحضور بشكل خاطئ ورقم كبير بدلاً من أن يكون مطابقاً لعدد الأيام المحددة في الفترة.

## السبب
كان يتم تعيين `start_date` و `end_date` إلى `null` للمتدربين المحذوفين في دالة `getAllTraineesWithResignations` في نموذج `CompanyAttendanceReport`، مما يجعل حساب عدد أيام الحضور يعطي `count($days)` بدلاً من القيمة الصحيحة.

## الحل
تم إصلاح المشكلة من خلال:

### 1. تعديل نموذج CompanyAttendanceReport
- **الملف**: `app/Models/Back/CompanyAttendanceReport.php`
- **التغيير**: إزالة تعيين `start_date` و `end_date` إلى `null` للمتدربين المحذوفين
- **السطر**: 185-186

```php
// قبل التعديل
$attendanceRecord->start_date = null;
$attendanceRecord->end_date = null;

// بعد التعديل
// For deleted trainees, we should not reset start_date and end_date to null
// Instead, we should keep the original values to maintain correct attendance calculation
// The view will handle the display logic for deleted trainees
```

### 2. تعديل ملفات العرض
تم تعديل جميع ملفات العرض لتتعامل مع المتدربين المحذوفين بشكل صحيح:

#### ملف special-company-simple.blade.php
- **التغيير**: إزالة تعيين التواريخ إلى `null` في العرض
- **السطر**: 280-290

#### ملف show.blade.php
- **التغيير**: إضافة منطق للتعامل مع المتدربين المحذوفين
- **المواقع**: 4 مواقع مختلفة في الملف

#### ملف one-table.blade.php
- **التغيير**: إضافة منطق للتعامل مع المتدربين المحذوفين
- **المواقع**: 2 موقع في الملف

#### ملف individual-table.blade.php
- **التغيير**: إضافة منطق للتعامل مع المتدربين المحذوفين
- **المواقع**: 2 موقع في الملف

## النتيجة
الآن المتدربين المحذوفين سيظهر لهم نفس عدد أيام الحضور مثل المتدربين النشطين، وسيتم حسابها بشكل صحيح بناءً على الفترة المحددة في التقرير.

## الملفات المعدلة
1. `app/Models/Back/CompanyAttendanceReport.php`
2. `resources/views/pdf/company-attendance-report/special-company-simple.blade.php`
3. `resources/views/pdf/company-attendance-report/show.blade.php`
4. `resources/views/pdf/company-attendance-report/one-table.blade.php`
5. `resources/views/pdf/company-attendance-report/individual-table.blade.php`

## تاريخ التعديل
تم إجراء هذه التعديلات في: {{ date('Y-m-d H:i:s') }}
