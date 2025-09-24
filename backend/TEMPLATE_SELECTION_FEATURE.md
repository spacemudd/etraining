# ميزة اختيار قالب تقرير الحضور والانصراف

## نظرة عامة
تم إضافة ميزة اختيار القالب لتقارير الحضور والانصراف، مما يسمح للمستخدمين باختيار القالب المناسب من ثلاثة قوالب متاحة.

## القوالب المتاحة

### 1. القالب الافتراضي (Default)
- **الملف**: `show.blade.php` أو `one-table.blade.php`
- **الاستخدام**: للتقارير العادية
- **المميزات**: تصميم تقليدي مع جداول واضحة

### 2. القالب المبسط (Simple)
- **الملف**: `special-company-simple.blade.php`
- **الاستخدام**: للشركات التي تواجه مشاكل SSL
- **المميزات**: تصميم مبسط مع ألوان مميزة للحضور والغياب

### 3. القالب الحديث (Modern)
- **الملف**: `special-company-modern.blade.php`
- **الاستخدام**: للتقارير الحديثة
- **المميزات**: تصميم عصري مع تحسينات الطباعة والتخطيط

## التحديثات المطبقة

### 1. قاعدة البيانات
```sql
-- Migration: add_template_type_to_company_attendance_reports_table
ALTER TABLE company_attendance_reports 
ADD COLUMN template_type ENUM('default', 'simple', 'modern') DEFAULT 'default' 
AFTER with_logo;
```

### 2. النماذج (Models)
```php
// CompanyAttendanceReport.php
protected $fillable = [
    // ... existing fields
    'template_type',
];
```

### 3. المتحكم (Controller)
```php
// CompanyAttendanceReportController.php
public function store(Request $request) {
    $report = CompanyAttendanceReport::create([
        // ... existing fields
        'template_type' => $request->template_type ?? 'default',
    ]);
}

public function update($id, Request $request) {
    $request->validate([
        // ... existing validation
        'template_type' => 'nullable|in:default,simple,modern',
    ]);
    
    $report->update([
        // ... existing fields
        'template_type' => $request->template_type ?? 'default',
    ]);
}
```

### 4. واجهة المستخدم (Frontend)
```vue
<!-- Create.vue & Edit.vue -->
<div class="col-span-6 sm:col-span-4">
    <jet-label for="template_type" :value="$t('words.template-type')" />
    <select class="mt-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" 
            v-model="createAttendanceReportForm.template_type">
        <option value="default">{{ $t('words.default-template') }}</option>
        <option value="simple">{{ $t('words.simple-template') }}</option>
        <option value="modern">{{ $t('words.modern-template') }}</option>
    </select>
    <jet-input-error class="mt-2" />
</div>
```

### 5. الخدمة (Service)
```php
// CompanyAttendanceReportService.php
public function makePdf($id) {
    // ... existing logic
    
    // Use template based on user selection or default
    switch ($report->template_type ?? 'default') {
        case 'simple':
            $view = 'pdf.company-attendance-report.special-company-simple';
            break;
        case 'modern':
            $view = 'pdf.company-attendance-report.special-company-modern';
            break;
        default:
            $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
            break;
    }
    
    // ... rest of the method
}
```

## كيفية الاستخدام

### 1. إنشاء تقرير جديد
1. انتقل إلى صفحة إنشاء تقرير الحضور
2. اختر الشركة والفترة الزمنية
3. اختر نوع القالب من القائمة المنسدلة
4. احفظ التقرير

### 2. تعديل تقرير موجود
1. انتقل إلى صفحة التقرير
2. اضغط على "تعديل"
3. اختر نوع القالب الجديد
4. احفظ التغييرات

### 3. معاينة التقرير
- سيتم استخدام القالب المختار تلقائياً عند معاينة أو طباعة التقرير

## الأولوية في اختيار القالب

1. **الشركات الخاصة**: إذا كانت الشركة مدرجة في قائمة الشركات الخاصة، سيتم استخدام القالب المخصص لها
2. **القالب المختار**: إذا لم تكن الشركة خاصة، سيتم استخدام القالب المختار من قبل المستخدم
3. **القالب الافتراضي**: إذا لم يتم اختيار قالب، سيتم استخدام القالب الافتراضي

## الملفات المعدلة

### Frontend
- `resources/js/Pages/Back/Reports/CompanyAttendance/Create.vue`
- `resources/js/Pages/Back/Reports/CompanyAttendance/Edit.vue`

### Backend
- `app/Http/Controllers/Back/CompanyAttendanceReportController.php`
- `app/Models/Back/CompanyAttendanceReport.php`
- `app/Services/CompanyAttendanceReportService.php`

### Database
- `database/migrations/2025_09_24_122148_add_template_type_to_company_attendance_reports_table.php`

## الاختبار

### 1. اختبار إنشاء تقرير جديد
- [ ] اختبار القالب الافتراضي
- [ ] اختبار القالب المبسط
- [ ] اختبار القالب الحديث

### 2. اختبار تعديل تقرير موجود
- [ ] تغيير القالب من الافتراضي إلى المبسط
- [ ] تغيير القالب من المبسط إلى الحديث
- [ ] تغيير القالب من الحديث إلى الافتراضي

### 3. اختبار معاينة التقارير
- [ ] التأكد من استخدام القالب الصحيح
- [ ] اختبار جودة الطباعة
- [ ] اختبار التخطيط على صفحات متعددة

## ملاحظات مهمة

1. **التوافق مع الإصدارات السابقة**: التقارير الموجودة ستحتفظ بالقالب الافتراضي
2. **الأداء**: لا يوجد تأثير على الأداء عند استخدام القوالب المختلفة
3. **الطباعة**: جميع القوالب محسنة للطباعة مع دعم تقسيم الصفحات
4. **الترجمة**: يجب إضافة ترجمات جديدة للحقول الجديدة في ملفات اللغة

## التطوير المستقبلي

1. **إضافة قوالب جديدة**: يمكن إضافة قوالب جديدة بسهولة
2. **تخصيص القوالب**: إمكانية تخصيص القوالب حسب الشركة
3. **معاينة القوالب**: إضافة معاينة سريعة للقالب قبل الحفظ
4. **إحصائيات الاستخدام**: تتبع أكثر القوالب استخداماً

## الدعم الفني

في حالة وجود مشاكل أو استفسارات حول هذه الميزة، يرجى التواصل مع فريق التطوير.
