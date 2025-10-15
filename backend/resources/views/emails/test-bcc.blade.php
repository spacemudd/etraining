<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BCC Email Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .content {
            margin-bottom: 30px;
        }
        .info-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .emoji {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="emoji">🧪</span> BCC Email Test</h1>
            <p>اختبار وظيفة BCC في النظام</p>
        </div>

        <div class="content">
            <div class="info-box success">
                <h3><span class="emoji">✅</span> تم إرسال الإيميل بنجاح!</h3>
                <p>{{ $message }}</p>
            </div>

            <h3><span class="emoji">📋</span> تفاصيل الاختبار:</h3>
            <ul>
                <li><strong>وقت الإرسال:</strong> {{ $timestamp }}</li>
                <li><strong>معرف الاختبار:</strong> {{ $test_id }}</li>
                <li><strong>الهدف:</strong> التأكد من وصول جميع ايميلات BCC</li>
            </ul>

            <div class="info-box">
                <h4><span class="emoji">🔍</span> ما يجب التحقق منه:</h4>
                <ol>
                    <li>وصول الإيميل لجميع المستلمين في TO</li>
                    <li>وصول الإيميل لجميع المستلمين في CC</li>
                    <li><strong>وصول الإيميل لجميع المستلمين في BCC</strong></li>
                    <li>عدم ظهور عناوين BCC للمستلمين الآخرين</li>
                </ol>
            </div>

            <div class="info-box">
                <h4><span class="emoji">🛠️</span> الإصلاحات المطبقة:</h4>
                <ul>
                    <li>معالجة منفصلة لكل نوع إيميل (TO, CC, BCC)</li>
                    <li>تحقق من صحة الايميلات</li>
                    <li>إزالة المسافات الزائدة</li>
                    <li>معالجة الحالات الخاصة</li>
                    <li>تسجيل مفصل للعمليات</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><span class="emoji">🚀</span> نظام التدريب الإلكتروني</p>
            <p>تم إنشاء هذا الإيميل تلقائياً لاختبار وظيفة BCC</p>
        </div>
    </div>
</body>
</html>
