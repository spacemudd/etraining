# 🚀 دليل الاختبار السريع لإيميل إجازة الوضع

## خطوات الاختبار على السيرفر

### 1. تحضير الاختبار

```bash
# الانتقال لمجلد المشروع
cd /path/to/your/project

# مراقبة الـ logs في terminal منفصل
tail -f storage/logs/laravel.log | grep -i "maternity\|email"
```

### 2. اختبار من المتصفح

1. **افتح المتصفح واذهب للموقع**
2. **افتح Developer Tools (F12)**
3. **اذهب لتبويب Console**
4. **اذهب لصفحة أي متدرب**
5. **انقر على "طلب إجازة جديد"**

### 3. تعبئة البيانات

1. **اختر "أجازة وضع"** من نوع الإجازة
2. **املأ التواريخ**
3. **ارفق ملف**
4. **تأكد من تفعيل "إرسال إشعار بالبريد الإلكتروني"**
5. **تحقق من ملء الحقول تلقائياً:**
   - TO: يجب أن يحتوي على إيميل الشركة
   - BCC: يجب أن يحتوي على الإيميلات المحددة

### 4. راقب الـ Console

يجب أن ترى:
```
Loading email defaults for trainee: X
Email defaults loaded successfully: {...}
Email form fields populated: {...}
Creating maternity leave request: {...}
Email data being sent: {...}
Leave request created successfully
```

### 5. راقب الـ Server Logs

يجب أن ترى تسلسل:
```
getEmailDefaults Called
Trainee Found for Email Defaults  
Email Defaults Prepared Successfully
Maternity Leave Email Process Started
sendMaternityLeaveEmail Method Called
TO Emails Prepared: [...]
BCC Emails Prepared: [...]
Recipients Summary: {...}
MaternityLeaveMail Constructor Called
MaternityLeaveMail Build Method Called
About to Send Email
Maternity Leave Email Sent Successfully
```

## 🔍 نقاط التحقق

### ✅ إذا نجح الاختبار:
- [ ] ظهرت جميع الـ logs بالتسلسل الصحيح
- [ ] تم إرسال الإيميل بنجاح  
- [ ] وصل الإيميل للمستلمين
- [ ] محتوى الإيميل صحيح

### ❌ إذا فشل الاختبار:

#### مشكلة 1: لم تظهر logs Frontend
**السبب**: خطأ في JavaScript
**الحل**: فحص Console للأخطاء

#### مشكلة 2: لم تظهر logs Backend  
**السبب**: لم تصل البيانات للسيرفر
**الحل**: فحص Network tab في Developer Tools

#### مشكلة 3: ظهرت logs لكن فشل الإرسال
**السبب**: خطأ في إعدادات البريد الإلكتروني
**الحل**: فحص إعدادات SMTP في .env

#### مشكلة 4: تم الإرسال لكن لم تصل الإيميلات
**السبب**: مشكلة في مزود البريد أو spam
**الحل**: 
- فحص مجلد spam
- اختبار مع إيميلات مختلفة
- التحقق من DNS settings

## 📧 اختبار إعدادات البريد

```bash
# اختبار سريع لإعدادات البريد
php artisan tinker

# داخل tinker:
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

## 🛠️ إعدادات مطلوبة

تأكد من وجود هذه الإعدادات في `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="Your Name"
```

## 📋 checklist سريع

- [ ] المشروع يعمل بدون أخطاء
- [ ] قاعدة البيانات متصلة
- [ ] المتدرب موجود وله شركة
- [ ] الشركة لها إيميل
- [ ] إعدادات SMTP صحيحة
- [ ] JavaScript يعمل في المتصفح
- [ ] الـ routes موجودة وتعمل

---

**الآن النظام جاهز مع logs شاملة لتتبع كل خطوة في عملية إرسال إيميل إجازة الوضع!**
