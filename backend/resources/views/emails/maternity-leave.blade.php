<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار إجازة وضع</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0056b3;
            font-size: 24px;
            margin: 0;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .content strong {
            color: #0056b3;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid #eeeeee;
            color: #777777;
            font-size: 14px;
        }
        .highlight {
            background-color: #e6f7ff;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: right;
        }
        .details h3 {
            text-align: right;
            margin-top: 0;
        }
        .details ul {
            margin: 0;
            padding-right: 20px;
            direction: rtl;
        }
        .details li {
            margin-bottom: 8px;
            text-align: right;
        }
        .content {
            text-align: right;
        }
        .content p {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>إشعار إجازة وضع</h1>
        </div>
        <div class="content">
            <p><strong>السلام عليكم ورحمة الله وبركاته،</strong></p>
            
            <p>نود إحاطتكم علماً بأن السيدة <strong>{{ $trainee->name }}</strong> قد تم منحها إجازة وضع اعتباراً من تاريخ <span class="highlight">{{ \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d') }}</span> وحتى تاريخ <span class="highlight">{{ \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d') }}</span>.</p>
            
            <p>وبناءً على نظام التأمينات الاجتماعية واللوائح المعمول بها، نود إحاطتكم علماً بأن هذه الإجازة مدفوعة الأجر من التأمينات الاجتماعية، وبالتالي لا يُصرف للموظفة راتب عن فترة الإجازة المحددة أعلاه.</p>
            
            <div class="details">
                <h3>تفاصيل الإجازة:</h3>
                <ul>
                    <li><strong>اسم الموظفة:</strong> {{ $trainee->name }}</li>
                    <li><strong>نوع الإجازة:</strong> {{ $leave->leave_type }}</li>
                    <li><strong>تاريخ بداية الإجازة:</strong> {{ \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d') }}</li>
                    <li><strong>تاريخ نهاية الإجازة:</strong> {{ \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d') }}</li>
                    <li><strong>تاريخ الطلب:</strong> {{ \Carbon\Carbon::parse($leave->created_at)->format('Y-m-d H:i') }}</li>
                </ul>
            </div>
            
            @if($leave->notes)
            <div class="details">
                <h3>ملاحظات إضافية:</h3>
                <p>{{ $leave->notes }}</p>
            </div>
            @endif
        </div>
        <div class="footer">
            <p><strong>مع تحياتنا،</strong></p>
        </div>
    </div>
</body>
</html>
