# إصلاح مشكلة القالب المتدرج - Template Type Empty String Fix

## المشكلة
كانت المشكلة أن `template_type` في قاعدة البيانات يحتوي على string فارغ (`""`) بدلاً من `null` أو قيمة صحيحة، مما يؤدي إلى أن النظام يستخدم القالب الافتراضي بدلاً من القالب المختار.

## السبب
- `??` operator في PHP يعمل فقط مع `null` وليس مع strings فارغة
- عندما يكون `template_type = ""` فإن `$report->template_type ?? 'default'` يعيد `""` وليس `'default'`
- هذا يؤدي إلى دخول `default` case في switch statement

## الحل المطبق

### 1. إصلاح الخدمة (Service)
```php
// app/Services/CompanyAttendanceReportService.php

// قبل الإصلاح
switch ($report->template_type ?? 'default') {

// بعد الإصلاح
$templateType = $report->template_type;
if (empty($templateType) || $templateType === '') {
    $templateType = 'default';
}
switch ($templateType) {
```

### 2. إصلاح المتحكم (Controller)
```php
// app/Http/Controllers/Back/CompanyAttendanceReportController.php

// قبل الإصلاح
$report->update([
    'template_type' => $request->template_type,
]);

// بعد الإصلاح
$templateType = $request->template_type;
if (empty($templateType) || $templateType === '') {
    $templateType = 'default';
}
$report->update([
    'template_type' => $templateType,
]);
```

### 3. إصلاح واجهة المستخدم (Frontend)
```javascript
// resources/js/Pages/Back/Reports/CompanyAttendance/Show.vue

// قبل الإصلاح
template_type: this.report.template_type || 'default',

// بعد الإصلاح
template_type: this.report.template_type && this.report.template_type !== '' ? this.report.template_type : 'default',
```

## الملفات المحدثة

1. **app/Services/CompanyAttendanceReportService.php**
   - إصلاح منطق اختيار القالب في دالة `makePdf`
   - إصلاح منطق اختيار القالب في دالة `makeIndividualPdf`

2. **app/Http/Controllers/Back/CompanyAttendanceReportController.php**
   - إصلاح دالة `updateTemplate` للتعامل مع القيم الفارغة
   - تحديث رسالة النجاح لتستخدم القيمة المعالجة

3. **resources/js/Pages/Back/Reports/CompanyAttendance/Show.vue**
   - إصلاح تهيئة `templateForm` للتعامل مع القيم الفارغة

## النتيجة
الآن عندما يكون `template_type` فارغ (`""`) في قاعدة البيانات:
- النظام سيتعامل معه كقيمة فارغة ويستخدم `'default'` كقيمة افتراضية
- عند اختيار القالب المتدرج، سيتم حفظ القيمة بشكل صحيح
- عند عرض التقرير، سيتم استخدام القالب المختار بشكل صحيح

## الاختبار
لاختبار الإصلاح:
1. انتقل إلى تقرير الحضور
2. اختر "القالب المتدرج"
3. اضغط "حفظ القالب"
4. معاينة التقرير - يجب أن يظهر بالتصميم المتدرج الجديد

## ملاحظات مهمة
- هذا الإصلاح لا يتطلب migrations جديدة
- يعمل مع البيانات الموجودة في قاعدة البيانات
- يحافظ على التوافق مع الإصدارات السابقة
- يعالج جميع الحالات المحتملة للقيم الفارغة
