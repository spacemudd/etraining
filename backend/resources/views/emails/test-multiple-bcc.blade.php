<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiple BCC Test</title>
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
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #28a745;
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
        .warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
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
        .email-list {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
        }
        .email-list ol {
            margin: 0;
            padding-left: 20px;
        }
        .test-id {
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="emoji">🧪</span> Multiple BCC Test</h1>
            <p>اختبار إرسال عدة ايميلات BCC</p>
        </div>

        <div class="content">
            <div class="info-box success">
                <h3><span class="emoji">✅</span> تم إرسال الإيميل بنجاح!</h3>
                <p>هذا إيميل اختبار للتأكد من وصول جميع ايميلات BCC</p>
            </div>

            <h3><span class="emoji">📋</span> تفاصيل الاختبار:</h3>
            <ul>
                <li><strong>معرف الاختبار:</strong> <span class="test-id">{{ $test_id }}</span></li>
                <li><strong>وقت الإرسال:</strong> {{ $timestamp }}</li>
                <li><strong>عدد BCC emails:</strong> {{ $bcc_count }}</li>
                @if(isset($method))
                <li><strong>الطريقة المستخدمة:</strong> {{ $method === 'loop' ? 'Loop Method (Old)' : 'Array Method (New)' }}</li>
                @endif
            </ul>

            <div class="email-list">
                <h4><span class="emoji">📧</span> BCC Emails المرسل إليها:</h4>
                <ol>
                    @foreach($bcc_emails as $email)
                    <li>{{ $email }}</li>
                    @endforeach
                </ol>
            </div>

            <div class="info-box warning">
                <h4><span class="emoji">⚠️</span> مهم جداً:</h4>
                <p>يجب أن يصل هذا الإيميل إلى <strong>جميع</strong> العناوين المذكورة أعلاه. إذا لم يصل لأي منها، فهناك مشكلة في إعدادات BCC.</p>
            </div>

            <div class="info-box">
                <h4><span class="emoji">🔍</span> كيفية التحقق:</h4>
                <ol>
                    <li>تحقق من صندوق الوارد لكل إيميل في القائمة أعلاه</li>
                    <li>تأكد من وصول إيميل بنفس معرف الاختبار: <span class="test-id">{{ $test_id }}</span></li>
                    <li>إذا لم يصل لأي إيميل، تحقق من مجلد الرسائل غير المرغوب فيها</li>
                    <li>راجع logs الخادم للتأكد من عدم وجود أخطاء</li>
                </ol>
            </div>

            @if(isset($method) && $method === 'loop')
            <div class="info-box warning">
                <h4><span class="emoji">🔄</span> طريقة Loop (القديمة):</h4>
                <p>هذا الإيميل تم إرساله باستخدام الطريقة القديمة (loop). قد يصل لإيميل واحد فقط بسبب مشكلة في Laravel Mail.</p>
            </div>
            @else
            <div class="info-box success">
                <h4><span class="emoji">🆕</span> طريقة Array (الجديدة):</h4>
                <p>هذا الإيميل تم إرساله باستخدام الطريقة الجديدة (array). يجب أن يصل لجميع ايميلات BCC.</p>
            </div>
            @endif
        </div>

        <div class="footer">
            <p><span class="emoji">🚀</span> نظام التدريب الإلكتروني</p>
            <p>تم إنشاء هذا الإيميل تلقائياً لاختبار وظيفة Multiple BCC</p>
        </div>
    </div>
</body>
</html>
