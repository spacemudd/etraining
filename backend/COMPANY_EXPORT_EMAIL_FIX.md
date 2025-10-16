# ✅ تم تعديل حقل "ايميل المندوب" في تصدير الشركات

## 🎯 التغيير المطبق:

تم تعديل ملف `app/Exports/CompaniesExport.php` لتغيير مصدر بيانات حقل "ايميل المندوب".

### 🔧 التغيير:

**قبل التعديل:**
```php
public function map($company): array
{
    return [
        $company->code,
        $company->created_at->format('Y-m-d'),
        optional($company->center)->name,
        $company->name_ar,
        $company->nature_of_work,
        optional($company->region)->name,
        $company->email,                    // الايميل الأساسي
        $company->salesperson_email,        // ايميل المندوب (من حقل منفصل)
        $company->cr_number,
        $company->trainees_count,
    ];
}
```

**بعد التعديل:**
```php
public function map($company): array
{
    return [
        $company->code,
        $company->created_at->format('Y-m-d'),
        optional($company->center)->name,
        $company->name_ar,
        $company->nature_of_work,
        optional($company->region)->name,
        $company->email,                    // الايميل الأساسي
        $company->email,                    // ايميل المندوب (نفس الايميل الأساسي)
        $company->cr_number,
        $company->trainees_count,
    ];
}
```

## 📊 النتيجة:

### قبل التعديل:
- **الايميل**: يأتي من حقل `email` في جدول الشركة
- **ايميل المندوب**: يأتي من حقل `salesperson_email` في جدول الشركة

### بعد التعديل:
- **الايميل**: يأتي من حقل `email` في جدول الشركة
- **ايميل المندوب**: يأتي من حقل `email` في جدول الشركة (نفس الايميل الأساسي)

## 🎯 الأعمدة في Excel:

1. **الرقم** - رقم الشركة
2. **تاريخ الإنشاء** - تاريخ إنشاء الشركة
3. **المركز** - اسم المركز
4. **الشركة** - اسم الشركة بالعربية
5. **طبيعة العمل** - طبيعة عمل الشركة
6. **المنطقة** - المنطقة الجغرافية
7. **الايميل** - ايميل الشركة الأساسي
8. **ايميل المندوب** - ايميل الشركة الأساسي (نفس العمود السابق)
9. **السجل التجاري** - رقم السجل التجاري
10. **عدد المتدربين** - عدد المتدربين المرتبطين بالشركة

## 🧪 كيفية الاختبار:

1. اذهب إلى صفحة الشركات: `prod.jasarah-ksa.com/back/companies`
2. اضغط على زر "إصدار" (Export)
3. افتح ملف Excel المحمل
4. تحقق من عمود "ايميل المندوب" - يجب أن يحتوي على نفس قيم عمود "الايميل"

## ✅ الخلاصة:

**تم تعديل حقل "ايميل المندوب" ليعرض ايميل الشركة الأساسي بدلاً من ايميل المندوب المنفصل.**

الآن عندما تقوم بتصدير الشركات، ستحصل على ايميل الشركة في عمود "ايميل المندوب" كما طلبت. 🚀
