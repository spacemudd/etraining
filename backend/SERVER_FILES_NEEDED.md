# الملفات المطلوبة على السيرفر لحل Server Error

## المشكلة
السيرفر يعطي server error عند محاولة عرض التقرير بالقالب المضغوط لأن:
1. ملفات القوالب المضغوطة غير موجودة على السيرفر
2. منطق الخدمة لم يتم تحديثه على السيرفر

## الملفات المطلوبة

### 1. ملف القالب المضغوط الجماعي
**المسار**: `resources/views/pdf/company-attendance-report/special-company-mini.blade.php`

**المحتوى**: يجب نسخ محتوى الملف المحلي إلى السيرفر

### 2. ملف القالب المضغوط الفردي
**المسار**: `resources/views/pdf/company-attendance-report/special-company-individual-mini.blade.php`

**المحتوى**: يجب نسخ محتوى الملف المحلي إلى السيرفر

### 3. تحديث ملف الخدمة
**المسار**: `app/Services/CompanyAttendanceReportService.php`

**التحديث المطلوب**: استبدال منطق اختيار القالب في دالتي `makePdf` و `makeIndividualPdf`

### 4. تحديث Controller
**المسار**: `app/Http/Controllers/Back/CompanyAttendanceReportController.php`

**التحديثات المطلوبة**:
- إضافة `'mini'` في validation
- إضافة case في `getTemplateName()`

### 5. تحديث Model
**المسار**: `app/Models/Back/CompanyAttendanceReport.php`

**التأكد من**: وجود `'template_type'` في `$fillable`

## الحل السريع

### الخطوة 1: رفع ملفات القوالب
نسخ الملفين التاليين من المحلي إلى السيرفر:
- `special-company-mini.blade.php`
- `special-company-individual-mini.blade.php`

### الخطوة 2: تحديث منطق الخدمة
في `app/Services/CompanyAttendanceReportService.php`:

**البحث عن السطر 279 واستبداله:**
```php
// استبدال هذا الكود
if (in_array($report->company->id, [

// بهذا الكود
switch ($report->template_type ?? 'default') {
    case 'simple':
        $view = 'pdf.company-attendance-report.special-company-simple';
        break;
    case 'modern':
        $view = 'pdf.company-attendance-report.special-company-modern';
        break;
    case 'mini':
        $view = 'pdf.company-attendance-report.special-company-mini';
        break;
    default:
        if (in_array($report->company->id, [
            '9ef83749-d1ba-44a5-82a9-f726840e02db',
            '92d30511-77a8-4290-8d20-419f93ede3fd',
            '19762266-e0fc-43e5-b6ae-b4deec886bb1',
            '73017d20-40c8-401f-8dc1-b36ca0416e35',
            '077e3421-a623-49f4-b3f2-dcf80c9d295f',
            'b455f112-ff48-4647-8db6-a3d365a3d0a3',
            '2d8b0e51-5ea6-4c4d-9c38-ec38429cb74e',
            '0e0e3d03-a9ad-4964-8c5a-6826cc5b0c6f',
        ])) {
            $view = 'pdf.company-attendance-report.special-company-simple';
        } elseif (in_array($report->company->id, [
            '2ea73041-e686-4093-b830-260b488eb014',
        ])) {
            $view = 'pdf.company-attendance-report.special-company-modern';
        } else {
            $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
        }
        break;
}
```

**نفس التحديث للدالة الثانية (حوالي السطر 382)**

### الخطوة 3: تحديث Controller
في `app/Http/Controllers/Back/CompanyAttendanceReportController.php`:

**تحديث validation:**
```php
'template_type' => 'required|in:default,simple,modern,mini',
```

**إضافة case في getTemplateName:**
```php
case 'mini':
    return 'القالب المضغوط';
```

## التحقق من النجاح

بعد رفع التحديثات:
1. اختر القالب المضغوط من صفحة التقرير
2. احفظ القالب
3. اضغط معاينة PDF
4. يجب أن ترى التصميم البرتقالي المضغوط

## ملاحظة مهمة
إذا استمر Server Error، تحقق من:
1. وجود ملفات القوالب على السيرفر
2. تحديث منطق الخدمة
3. مسح الـ cache: `php artisan view:clear`
