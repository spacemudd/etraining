# تنفيذ تصميم خاص لشيت الحضور للشركات المحددة

## نظرة عامة
تم تنفيذ تصميم مختلف لشيت الحضور والانصراف للشركات التالية:
- `9ef83749-d1ba-44a5-82a9-f726840e02db` - مصنع هلال مشبب العتيبي
- `92d30511-77a8-4290-8d20-419f93ede3fd` - الشركة الجديدة

## الملفات المضافة/المعدلة

### 1. ملفات العرض الجديدة
- `resources/views/pdf/company-attendance-report/special-company.blade.php` - تصميم خاص للتقرير الجماعي
- `resources/views/pdf/company-attendance-report/special-company-individual.blade.php` - تصميم خاص للتقرير الفردي

### 2. ملف CSS منفصل
- `public/css/special-company-pdf.css` - أنماط التصميم الخاص

### 3. ملف الخدمة المعدل
- `app/Services/CompanyAttendanceReportService.php` - تم تعديله لاستخدام التصميم الخاص

## التغييرات المطبقة

### في CompanyAttendanceReportService.php

#### دالة makePdf()
```php
// Check if this is the special company to use different design
if (in_array($report->company->id, [
    '9ef83749-d1ba-44a5-82a9-f726840e02db', // مصنع هلال مشبب العتيبي
    '92d30511-77a8-4290-8d20-419f93ede3fd', // الشركة الجديدة
])) {
    $view = 'pdf.company-attendance-report.special-company';
} else {
    $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
}
```

#### دالة makeIndividualPdf()
```php
// Check if this is the special company to use different design
if (in_array($record->company->id, [
    '9ef83749-d1ba-44a5-82a9-f726840e02db', // مصنع هلال مشبب العتيبي
    '92d30511-77a8-4290-8d20-419f93ede3fd', // الشركة الجديدة
])) {
    $view = 'pdf.company-attendance-report.special-company-individual';
} else {
    $view = 'pdf.company-attendance-report.individual-table';
}
```

## مميزات التصميم الجديد

### 1. تصميم عصري وجذاب
- خلفية متدرجة ملونة
- ظلال وحواف مدورة
- تأثيرات بصرية متقدمة

### 2. تخطيط محسن
- عرض معلومات الشركة بشكل منظم
- تفاصيل التقرير في بطاقات منفصلة
- جدول حضور محسن مع رموز ملونة

### 3. رموز بصرية واضحة
- ✓ للحضور (أخضر)
- ✗ للغياب (أحمر)
- X للإجازة (برتقالي)

### 4. تصميم متجاوب
- يعمل على جميع أحجام الشاشات
- أنماط طباعة محسنة

## كيفية الاستخدام

### للتقارير الجماعية
عند استخراج شيت الحضور كملف PDF، إذا كانت الشركة تحمل المعرف المحدد، سيتم تلقائياً استخدام التصميم الجديد.

### للتقارير الفردية
عند استخراج تقرير حضور فردي، سيتم أيضاً استخدام التصميم الجديد للشركة المحددة.

## التوافق

- يعمل مع جميع المتصفحات الحديثة
- يدعم الطباعة بشكل مثالي
- يحافظ على جميع الوظائف الموجودة
- لا يؤثر على الشركات الأخرى

## الصيانة

### إضافة شركات جديدة
لإضافة شركة أخرى للتصميم الخاص، أضف معرف الشركة في الشرط:

```php
if (in_array($report->company->id, [
    '9ef83749-d1ba-44a5-82a9-f726840e02db',
    'new-company-id-here'
])) {
    // استخدام التصميم الخاص
}
```

### تعديل التصميم
يمكن تعديل التصميم من خلال:
- تعديل ملف CSS: `public/css/special-company-pdf.css`
- تعديل ملفات العرض: `special-company.blade.php` و `special-company-individual.blade.php`

## الاختبار

### اختبار التصميم الجديد
1. تأكد من وجود شركة بالمعرف المحدد
2. قم بإنشاء تقرير حضور لهذه الشركة
3. استخرج التقرير كملف PDF
4. تأكد من ظهور التصميم الجديد

### اختبار التصميم القديم
1. قم بإنشاء تقرير حضور لشركة أخرى
2. استخرج التقرير كملف PDF
3. تأكد من ظهور التصميم القديم

## ملاحظات مهمة

- التصميم الجديد يستخدم خط Cairo العربي لتحسين عرض النصوص العربية
- جميع الألوان والأنماط قابلة للتخصيص بسهولة
- التصميم يحافظ على جميع البيانات والمعلومات الموجودة
- لا توجد تغييرات في قاعدة البيانات أو المنطق الأساسي 