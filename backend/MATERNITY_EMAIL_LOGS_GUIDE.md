# 📧 دليل تتبع logs إيميل إجازة الوضع

## الآن تم إضافة logs شاملة في جميع مراحل العملية

### 🔍 كيفية مراقبة الـ logs على السيرفر

#### 1. مراقبة الـ logs في الوقت الفعلي:
```bash
tail -f storage/logs/laravel.log | grep -i "maternity\|email"
```

#### 2. البحث في آخر 100 سطر:
```bash
tail -n 100 storage/logs/laravel.log | grep -i "maternity"
```

#### 3. البحث عن أخطاء محددة:
```bash
grep -i "error.*maternity" storage/logs/laravel.log
```

### 📋 تسلسل الـ logs المتوقع

عند إنشاء إجازة وضع مع تفعيل الإيميل، ستظهر الـ logs بهذا التسلسل:

#### 1. Frontend Logs (في console المتصفح):
```
Loading email defaults for trainee: {trainee_id}
Email defaults loaded successfully: {data}
Email form fields populated: {fields}
Creating maternity leave request: {request_data}
Email data being sent: {email_data}
Leave request created successfully: {response}
```

#### 2. Backend Logs (في storage/logs/laravel.log):

**أ. تحميل البيانات الافتراضية:**
```
getEmailDefaults Called
Trainee Found for Email Defaults
Email Defaults Prepared Successfully
```

**ب. إنشاء طلب الإجازة:**
```
Maternity Leave Email Process Started
```

**ج. إرسال الإيميل:**
```
sendMaternityLeaveEmail Method Called
TO Emails Prepared
CC Emails Prepared  
BCC Emails Prepared
Recipients Summary
MaternityLeaveMail Object Created
```

**د. إنشاء كائن الإيميل:**
```
MaternityLeaveMail Constructor Called
MaternityLeaveMail Build Method Called
Email Subject Prepared
MaternityLeaveMail Build Completed Successfully
```

**هـ. الإرسال النهائي:**
```
About to Send Email
Maternity Leave Email Sent Successfully
```

### ❌ الأخطاء المحتملة وما تعنيه

#### 1. `No Recipients Found for Email`
**المعنى**: لم يتم العثور على أي مستلم
**الحل**: التأكد من ملء حقول TO أو CC أو BCC

#### 2. `Error Sending Maternity Leave Email`
**المعنى**: خطأ في إرسال الإيميل
**الحل**: فحص إعدادات SMTP

#### 3. `Error Getting Email Defaults`
**المعنى**: خطأ في تحميل البيانات الافتراضية
**الحل**: فحص قاعدة البيانات والمتدرب

#### 4. `Maternity Leave Email Not Sent`
**المعنى**: لم يتم إرسال الإيميل (مقصود)
**السبب**: إما لم يتم تفعيل الإرسال أو نوع الإجازة ليس "أجازة وضع"

### 🧪 اختبار النظام

#### 1. اختبار من المتصفح:
1. افتح Developer Tools (F12)
2. اذهب لـ Console
3. قم بإنشاء إجازة وضع
4. راقب الـ logs في Console

#### 2. اختبار من السيرفر:
```bash
# في terminal منفصل
tail -f storage/logs/laravel.log

# في terminal آخر، قم بإنشاء إجازة وضع من المتصفح
```

### 📊 ما يجب مراقبته

#### ✅ عند نجاح العملية:
- تحميل البيانات الافتراضية
- إنشاء طلب الإجازة
- تحضير قوائم المستلمين
- إنشاء كائن الإيميل
- إرسال الإيميل بنجاح

#### ❌ عند فشل العملية:
- أي رسالة خطأ
- عدم ظهور بعض الخطوات
- فشل في تحضير المستلمين
- خطأ في إعدادات SMTP

### 🔧 نصائح للتشخيص

1. **ابدأ بـ Console المتصفح** - للتأكد من إرسال البيانات
2. **راقب logs السيرفر** - لفهم ما يحدث في Backend
3. **تحقق من المستلمين** - تأكد من وجود إيميلات في الحقول
4. **اختبر مع إيميل واحد** - ابدأ بإيميل واحد في TO
5. **فحص إعدادات SMTP** - تأكد من صحة إعدادات البريد

### 🚨 الأوامر السريعة للتشخيص

```bash
# مراقبة مباشرة
tail -f storage/logs/laravel.log | grep -i "maternity"

# البحث عن أخطاء
grep -i "error.*email" storage/logs/laravel.log | tail -10

# فحص آخر محاولة إرسال
grep -i "maternity.*email" storage/logs/laravel.log | tail -20
```

---

**ملاحظة**: تأكد من أن إعدادات البريد الإلكتروني صحيحة في ملف `.env` وأن السيرفر يدعم إرسال الإيميلات.
