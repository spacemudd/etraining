# ميزة اختيار القالب من صفحة التقرير

## نظرة عامة
تم تطوير ميزة اختيار القالب لتقارير الحضور والانصراف بحيث يمكن للمستخدمين اختيار القالب المناسب مباشرة من صفحة التقرير نفسه، مما يوفر مرونة أكبر وسهولة في الاستخدام.

## الميزات الجديدة

### 1. واجهة اختيار القالب التفاعلية
- **الموقع**: صفحة التقرير (`Show.vue`)
- **التصميم**: بطاقات تفاعلية مع أيقونات وألوان مميزة
- **الاستجابة**: تحديث فوري عند اختيار قالب جديد

### 2. القوالب المتاحة

#### القالب الافتراضي (Default)
- **الأيقونة**: � (مستند تقليدي)
- **اللون**: رمادي
- **الوصف**: للتقارير العادية والاستخدام العام

#### القالب المبسط (Simple)
- **الأيقونة**: ✅ (علامة صح)
- **اللون**: أخضر
- **الوصف**: للشركات التي تواجه مشاكل SSL

#### القالب الحديث (Modern)
- **الأيقونة**: � (مخططات)
- **اللون**: بنفسجي
- **الوصف**: للتقارير الحديثة والمحسنة

## التحديثات المطبقة

### 1. واجهة المستخدم (Frontend)

#### صفحة التقرير (`Show.vue`)
```vue
<!-- Template Selection Section -->
<div class="mt-10 container px-6 mx-auto">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('words.template-settings') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Template Cards -->
        </div>
    </div>
</div>
```

#### البيانات والطرق
```javascript
data() {
    return {
        templateForm: this.$inertia.form({
            template_type: this.report.template_type || 'default',
        }, {
            bag: 'templateForm',
            resetOnSuccess: false,
        }),
    }
},
methods: {
    selectTemplate(templateType) {
        this.templateForm.template_type = templateType;
    },
    updateTemplate() {
        this.templateForm.patch(route('back.reports.company-attendance.update-template', this.report.id), {
            preserveScroll: true,
            onSuccess: () => {
                this.$inertia.reload({ only: ['report'] });
            }
        });
    }
}
```

### 2. الخادم (Backend)

#### Route جديد
```php
// routes/web.php
Route::patch('company-attendance/{id}/template', [CompanyAttendanceReportController::class, 'updateTemplate'])
    ->name('reports.company-attendance.update-template');
```

#### Controller Method
```php
public function updateTemplate($id, Request $request)
{
    $request->validate([
        'template_type' => 'required|in:default,simple,modern',
    ]);

    $report = CompanyAttendanceReport::findOrFail($id);
    $report->update([
        'template_type' => $request->template_type,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم تحديث القالب بنجاح',
        'template_type' => $report->template_type
    ]);
}
```

### 3. التغييرات في صفحة الإنشاء
- ✅ **إزالة حقل اختيار القالب** من صفحة الإنشاء
- ✅ **تعيين القالب الافتراضي** تلقائياً عند الإنشاء
- ✅ **تبسيط عملية الإنشاء** للمستخدمين

## كيفية الاستخدام

### 1. إنشاء تقرير جديد
1. انتقل إلى صفحة إنشاء تقرير الحضور
2. اختر الشركة والفترة الزمنية
3. احفظ التقرير (سيتم استخدام القالب الافتراضي)

### 2. اختيار القالب من صفحة التقرير
1. انتقل إلى صفحة التقرير المطلوب
2. ابحث عن قسم "إعدادات القالب"
3. اختر القالب المناسب من البطاقات الثلاث
4. اضغط على "حفظ القالب"

### 3. معاينة التقرير
- سيتم استخدام القالب المختار تلقائياً عند معاينة أو طباعة التقرير

## المميزات الجديدة

### 1. واجهة مستخدم محسنة
- **تصميم بصري جذاب**: بطاقات ملونة مع أيقونات واضحة
- **تفاعل سلس**: تحديث فوري عند اختيار قالب جديد
- **استجابة سريعة**: حفظ سريع بدون إعادة تحميل الصفحة

### 2. مرونة في الاستخدام
- **تغيير القالب في أي وقت**: حتى بعد إنشاء التقرير
- **معاينة فورية**: يمكن رؤية التغيير فوراً
- **حفظ تلقائي**: لا حاجة لإعادة إنشاء التقرير

### 3. تحسين تجربة المستخدم
- **واجهة بديهية**: سهلة الفهم والاستخدام
- **تغذية راجعة بصرية**: إشارات واضحة للقالب المختار
- **عملية مبسطة**: خطوات أقل لتحقيق الهدف

## الملفات المعدلة

### Frontend
- `resources/js/Pages/Back/Reports/CompanyAttendance/Show.vue` ✅
- `resources/js/Pages/Back/Reports/CompanyAttendance/Create.vue` ✅ (إزالة الحقل)

### Backend
- `app/Http/Controllers/Back/CompanyAttendanceReportController.php` ✅
- `routes/web.php` ✅

### Database
- `database/migrations/2025_09_24_122148_add_template_type_to_company_attendance_reports_table.php` ✅

## الاختبار

### 1. اختبار الواجهة
- [ ] عرض القوالب الثلاث بشكل صحيح
- [ ] تمييز القالب المختار بصرياً
- [ ] تفاعل البطاقات عند النقر

### 2. اختبار الوظائف
- [ ] حفظ القالب الجديد
- [ ] تحديث البيانات في قاعدة البيانات
- [ ] استخدام القالب الصحيح عند المعاينة

### 3. اختبار التكامل
- [ ] عمل القوالب مع جميع أنواع التقارير
- [ ] التوافق مع الشركات الخاصة
- [ ] الأداء مع التقارير الكبيرة

## التطوير المستقبلي

### 1. تحسينات مقترحة
- **معاينة القالب**: إضافة معاينة سريعة للقالب قبل الحفظ
- **إحصائيات الاستخدام**: تتبع أكثر القوالب استخداماً
- **تخصيص القوالب**: إمكانية تخصيص القوالب حسب الشركة

### 2. ميزات إضافية
- **حفظ تفضيلات المستخدم**: تذكر آخر قالب استخدمه المستخدم
- **تصدير القوالب**: إمكانية تصدير القوالب المخصصة
- **إدارة القوالب**: واجهة لإدارة القوالب المتاحة

## الدعم الفني

في حالة وجود مشاكل أو استفسارات حول هذه الميزة، يرجى التواصل مع فريق التطوير.

---

**تاريخ التحديث**: 2025-09-24  
**الإصدار**: 2.0  
**الحالة**: مكتمل ومتاح للاستخدام
