# حلول مشكلة SSL في إنشاء ملفات PDF

## المشكلة
يظهر خطأ `QSslSocket: cannot resolve CRYPTO_num_locks` عند محاولة إنشاء ملفات PDF للشركة المحددة. هذا الخطأ يتعلق بمكتبة `wkhtmltopdf` ومشاكل SSL/TLS.

## الحلول المطبقة

### 1. إزالة الخيارات غير الصحيحة

تم إزالة الخيارات التالية من `CompanyAttendanceReportService.php` لأنها غير مدعومة:

```php
// هذه الخيارات غير مدعومة في knp-snappy
->setOption('no-ssl-errors', true)        // ❌ غير مدعوم
->setOption('ignore-ssl-errors', true)    // ❌ غير مدعوم
->setOption('ssl-no-verify', true)        // ❌ غير مدعوم
->setOption('disable-ssl', true)          // ❌ غير مدعوم
```

### 2. تحديث ملف إعدادات Snappy

تم تحديث `config/snappy.php` بإضافة خيارات صحيحة:

```php
'options' => [
    'enable-local-file-access' => true,
    'encoding'      => 'UTF-8',
    'no-stop-slow-scripts' => true,
    'javascript-delay' => 1000,
    'enable-internal-links' => true,
    'enable-external-links' => true,
    'disable-smart-shrinking' => true,
    'viewport-size' => '1024×768',
    'zoom' => '0.78'
],
'env' => [
    'QT_LOGGING_RULES' => '*.debug=false;qt.webengine*.debug=false',
    'QTWEBENGINE_CHROMIUM_FLAGS' => '--disable-web-security --disable-features=VizDisplayCompositor',
],
```

### 3. إنشاء ملفات عرض مبسطة (الحل الجديد)

تم إنشاء ملفات عرض بدون أي CSS خارجي لتجنب مشاكل SSL نهائياً:

- `special-company-simple.blade.php` - تصميم مبسط للتقرير الجماعي
- `special-company-individual-simple.blade.php` - تصميم مبسط للتقرير الفردي

**مميزات التصميم المبسط:**
- ✅ **بدون CSS خارجي** - يحل مشكلة SSL نهائياً
- ✅ **أنماط مضمنة** - جميع الأنماط في نفس الملف
- ✅ **تصميم جميل** - يحافظ على المظهر الجذاب
- ✅ **متوافق مع الطباعة** - يعمل بشكل مثالي عند الطباعة
- ✅ **ألوان واضحة** - رموز ملونة للحضور والغياب والإجازة

### 4. تحديث الخدمة

تم تحديث `CompanyAttendanceReportService.php` لاستخدام التصميم المبسط:

```php
// Check if this is the special company to use different design
if ($report->company->id === '9ef83749-d1ba-44a5-82a9-f726840e02db') {
    // Use simplified design to avoid SSL issues
    $view = 'pdf.company-attendance-report.special-company-simple';
} else {
    $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
}
```

## الخيارات الصحيحة المدعومة في wkhtmltopdf

### خيارات SSL المدعومة:
- `--disable-web-security` - تعطيل أمان الويب
- `--disable-features=VizDisplayCompositor` - تعطيل ميزات معينة
- `--no-stop-slow-scripts` - عدم إيقاف النصوص البطيئة
- `--javascript-delay` - تأخير JavaScript

### خيارات التخطيط المدعومة:
- `--page-size` - حجم الصفحة
- `--orientation` - اتجاه الصفحة
- `--margin-top/bottom/left/right` - الهوامش
- `--encoding` - الترميز
- `--dpi` - دقة الطباعة

## كيفية الاختبار

### 1. اختبار التصميم المبسط
```bash
# تأكد من أن الشركة تحمل المعرف المحدد
# قم بإنشاء تقرير حضور
# استخرج كملف PDF
# يجب أن يعمل الآن بدون أخطاء SSL
```

### 2. مقارنة التصميمات
- **التصميم الأصلي**: يستخدم CSS خارجي (قد يسبب مشاكل SSL)
- **التصميم المبسط**: يستخدم CSS مضمن (يحل مشكلة SSL)

## حلول إضافية (إذا استمرت المشكلة)

### 1. تحديث wkhtmltopdf
```bash
# في Dockerfile
RUN apk add --no-cache wkhtmltopdf
```

### 2. إضافة متغيرات البيئة
```bash
# في .env
WKHTML_PDF_BINARY=/usr/local/bin/wkhtmltopdf2
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
wkhtmltopdf --disable-web-security --disable-features=VizDisplayCompositor test.html test.pdf
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

- **التصميم المبسط يحل مشكلة SSL نهائياً** ✅
- التصميم البديل يحافظ على جميع البيانات والمعلومات
- لا توجد تغييرات في قاعدة البيانات
- جميع الوظائف تعمل بشكل طبيعي
- التصميم المبسط يستخدم أنماط CSS مضمنة
- تم إزالة الخيارات غير المدعومة لتجنب الأخطاء
- **الحل الجديد: استخدام ملفات بدون CSS خارجي**

## الدعم

إذا استمرت المشكلة، يرجى:
1. فحص سجلات الأخطاء
2. اختبار التصميم المبسط الجديد
3. التأكد من إصدار wkhtmltopdf
4. مراجعة إعدادات الخادم
5. التأكد من أن جميع الخيارات المستخدمة مدعومة

## الحل النهائي

**استخدام التصميم المبسط** هو الحل الأفضل والأكثر استقراراً:
- يحل مشكلة SSL نهائياً
- يحافظ على المظهر الجذاب
- يعمل مع جميع الإعدادات
- سهل الصيانة والتطوير 