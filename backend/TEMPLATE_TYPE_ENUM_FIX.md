# إصلاح مشكلة template_type enum

## المشكلة المكتشفة
عند محاولة تغيير `template_type` إلى `'mini'`، لا يتم حفظ القيمة وتعود إلى فارغة.

### السبب الجذري
Migration الأصلي لا يحتوي على `'mini'` في enum:

```php
// Migration القديم (المشكلة)
$table->enum('template_type', ['default', 'simple', 'modern'])->default('default');
```

هذا يعني أن قاعدة البيانات ترفض أي قيمة غير موجودة في enum.

## الحلول المطبقة

### 1. تحديث Migration الأصلي
تم تحديث `2025_09_24_122148_add_template_type_to_company_attendance_reports_table.php`:

```php
// Migration المحدث
$table->enum('template_type', ['default', 'simple', 'modern', 'mini'])->default('default');
```

### 2. إنشاء Migration جديد للتحديث
تم إنشاء `2025_09_25_093637_update_template_type_enum_in_company_attendance_reports_table.php`:

```php
public function up()
{
    // تحديث enum لإضافة 'mini'
    DB::statement("ALTER TABLE company_attendance_reports MODIFY COLUMN template_type ENUM('default', 'simple', 'modern', 'mini') DEFAULT 'default'");
}

public function down()
{
    // إرجاع enum إلى حالته السابقة
    DB::statement("ALTER TABLE company_attendance_reports MODIFY COLUMN template_type ENUM('default', 'simple', 'modern') DEFAULT 'default'");
}
```

### 3. ملف SQL مباشر للتحديث السريع
تم إنشاء `fix_template_type_enum.sql`:

```sql
-- تحديث enum لإضافة 'mini' إلى template_type
ALTER TABLE company_attendance_reports 
MODIFY COLUMN template_type ENUM('default', 'simple', 'modern', 'mini') DEFAULT 'default';

-- تحديث التقرير المحدد لاستخدام القالب المضغوط
UPDATE company_attendance_reports 
SET template_type = 'mini' 
WHERE id = 129289;

-- عرض النتيجة للتأكد
SELECT id, number, template_type FROM company_attendance_reports WHERE id = 129289;
```

## كيفية التطبيق على السيرفر

### الطريقة الأولى: استخدام Migration
```bash
php artisan migrate
```

### الطريقة الثانية: استخدام SQL مباشر
تشغيل محتوى ملف `fix_template_type_enum.sql` مباشرة في قاعدة البيانات.

## التحقق من النجاح

بعد التحديث، يجب أن يعمل هذا الكود:

```php
$c = CompanyAttendanceReport::where('number','ATR-129277')->first();
$c->template_type = 'mini';
$c->save();

// التحقق
echo $c->template_type; // يجب أن يطبع: mini
```

## الملفات المحدثة

1. ✅ `database/migrations/2025_09_24_122148_add_template_type_to_company_attendance_reports_table.php`
2. ✅ `database/migrations/2025_09_25_093637_update_template_type_enum_in_company_attendance_reports_table.php`
3. ✅ `fix_template_type_enum.sql`

## الحالة: مكتمل ✅

تم إصلاح مشكلة enum وإضافة دعم للقالب المضغوط!

**التاريخ**: 2025-09-25
**المشكلة**: enum لا يحتوي على 'mini'
**الحل**: تحديث enum وإضافة Migration جديد
