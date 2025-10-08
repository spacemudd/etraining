# إصلاح خطأ القالب المتدرج - Column 'date' Not Found

## المشكلة
كان يظهر خطأ `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'date' in 'WHERE'` عند محاولة عرض القالب المتدرج.

## السبب
المشكلة كانت في الـ queries في ملفات القالب المتدرج:
1. استخدام `->where('date', $day['date'])` بدلاً من `->whereDate('session_starts_at', $day['date'])`
2. استخدام `$attendance->status` بدلاً من `$attendance->attendance_status`

## الحل المطبق

### 1. إصلاح الـ Query للتاريخ
```php
// قبل الإصلاح
$attendance = $trainee->trainee->attendances()
    ->where('date', $day['date'])
    ->first();

// بعد الإصلاح
$attendance = $trainee->trainee->attendances()
    ->whereDate('session_starts_at', $day['date'])
    ->first();
```

### 2. إصلاح الـ Status
```php
// قبل الإصلاح
@if ($attendance->status === 'present')

// بعد الإصلاح
@if ($attendance->attendance_status === 'present')
```

## الملفات المحدثة

### 1. special-company-gradient.blade.php
- إصلاح جميع الـ queries للتاريخ
- إصلاح جميع الـ status checks
- تحديث الإحصائيات

### 2. special-company-individual-gradient.blade.php
- إصلاح جميع الـ queries للتاريخ
- إصلاح جميع الـ status checks
- تحديث الإحصائيات

## تفاصيل الإصلاحات

### الـ Column الصحيح
- **الخطأ**: `date` (غير موجود)
- **الصحيح**: `session_starts_at` (موجود في جدول `course_batch_session_attendances`)

### الـ Status الصحيح
- **الخطأ**: `$attendance->status` (column مباشر)
- **الصحيح**: `$attendance->attendance_status` (accessor method)

### قيم الـ Status المتاحة
- `present`: حضور
- `absent`: غياب
- `absent_forgiven`: غياب مع عذر
- `present_late`: حضور متأخر

## النتيجة
الآن القالب المتدرج يعمل بشكل صحيح:
- ✅ لا توجد أخطاء في قاعدة البيانات
- ✅ يتم عرض بيانات الحضور بشكل صحيح
- ✅ الإحصائيات تعمل بشكل صحيح
- ✅ التصميم المتدرج يظهر بشكل كامل

## الاختبار
1. اختر القالب المتدرج
2. اضغط "حفظ القالب"
3. اضغط "عرض" أو "معاينة"
4. يجب أن يظهر التقرير بالتصميم المتدرج بدون أخطاء
