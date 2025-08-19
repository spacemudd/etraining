# حلول مشكلة SSL في إنشاء ملفات PDF

## المشكلة
يظهر خطأ `QSslSocket: cannot resolve CRYPTO_num_locks` عند محاولة إنشاء ملفات PDF للشركة المحددة. هذا الخطأ يتعلق بمكتبة `wkhtmltopdf` ومشاكل SSL/TLS.

## الحلول المطبقة

### 1. إضافة خيارات SSL/TLS في إعدادات PDF

تم إضافة الخيارات التالية في `CompanyAttendanceReportService.php`:

```php
->setOption('no-ssl-errors', true)
->setOption('ignore-ssl-errors', true)
->setOption('ssl-no-verify', true)
->setOption('disable-ssl', true)
```

### 2. تحديث ملف إعدادات Snappy

تم تحديث `config/snappy.php` بإضافة خيارات SSL:

```php
'options' => [
    'enable-local-file-access' => true,
    'encoding'      => 'UTF-8',
    'no-ssl-errors' => true,
    'ignore-ssl-errors' => true,
    'ssl-no-verify' => true,
    'disable-ssl' => true,
    'no-stop-slow-scripts' => true,
    'javascript-delay' => 1000,
    'enable-internal-links' => true,
    'enable-external-links' => true
],
```

### 3. إنشاء ملفات عرض بديلة

تم إنشاء ملفات عرض بدون CSS خارجي لتجنب مشاكل SSL:

- `special-company-fallback.blade.php` - للتقرير الجماعي
- `special-company-individual-fallback.blade.php` - للتقرير الفردي

### 4. آلية Fallback

تم إضافة آلية احتياطية في الخدمة:

```php
// Try to use the special design, fallback to basic if SSL issues occur
try {
    $view = 'pdf.company-attendance-report.special-company';
} catch (Exception $e) {
    $view = 'pdf.company-attendance-report.special-company-fallback';
}
```

## كيفية الاختبار

### 1. اختبار التصميم الأصلي
```bash
# تأكد من أن الشركة تحمل المعرف المحدد
# قم بإنشاء تقرير حضور
# استخرج كملف PDF
```

### 2. اختبار التصميم البديل
إذا فشل التصميم الأصلي، سيتم تلقائياً استخدام التصميم البديل.

## حلول إضافية (إذا استمرت المشكلة)

### 1. تحديث wkhtmltopdf
```bash
# في Dockerfile
RUN apk add --no-cache wkhtmltopdf
```

### 2. إضافة متغيرات البيئة
```bash
# في .env
WKHTML_PDF_BINARY=/usr/local/bin/wkhtmltopdf
WKHTML_IMG_BINARY=/usr/local/bin/wkhtmltoimage
```

### 3. استخدام إصدار محدد من wkhtmltopdf
```dockerfile
# نسخ إصدار محدد
COPY --from=madnight/alpine-wkhtmltopdf-builder:0.12.5-alpine3.10-606718795 /bin/wkhtmltopdf /usr/bin/wkhtmltopdf
```

### 4. إضافة مكتبات SSL
```dockerfile
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    libssl1.1
```

## مراقبة الأخطاء

### 1. فحص السجلات
```bash
tail -f storage/logs/laravel.log
```

### 2. فحص أخطاء wkhtmltopdf
```bash
# اختبار مباشر
wkhtmltopdf --no-ssl-errors --ignore-ssl-errors --ssl-no-verify --disable-ssl test.html test.pdf
```

### 3. فحص إصدار المكتبة
```bash
wkhtmltopdf --version
```

## استكشاف الأخطاء

### 1. مشاكل SSL
- تأكد من أن الخادم يدعم HTTPS
- تحقق من شهادات SSL
- جرب تعطيل SSL مؤقتاً

### 2. مشاكل المكتبات
- تأكد من تثبيت جميع المكتبات المطلوبة
- تحقق من إصدارات المكتبات
- أعد تشغيل الخادم

### 3. مشاكل الصلاحيات
- تأكد من صلاحيات الملفات
- تحقق من صلاحيات المجلدات
- تأكد من صلاحيات المستخدم

## ملاحظات مهمة

- التصميم البديل يحافظ على جميع البيانات والمعلومات
- لا توجد تغييرات في قاعدة البيانات
- جميع الوظائف تعمل بشكل طبيعي
- التصميم البديل يستخدم أنماط CSS مضمنة

## الدعم

إذا استمرت المشكلة، يرجى:
1. فحص سجلات الأخطاء
2. اختبار إعدادات SSL
3. التأكد من إصدار wkhtmltopdf
4. مراجعة إعدادات الخادم 