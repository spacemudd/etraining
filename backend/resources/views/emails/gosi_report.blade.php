<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير GOSI</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }
        .section {
            border-top: 1px solid #ddd;
            margin-top: 20px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h2 style="text-align: right">📊 تقرير استخدام واجهة GOSI</h2>

    <div class="section">
        <p style="text-align: right"><strong>عدد الطلبات هذا الشهر ({{ $currentMonth }}):</strong> {{ $requestsUsed }} طلب</p>
        <p style="text-align: right"><strong>التكلفة حتى الآن:</strong> SAR {{ $costSoFarFormatted }}</p>
        <p style="text-align: right"><strong>المتبقي لهذا الشهر:</strong> {{ $requestsRemaining }} طلب</p>
    </div>

    <div class="section">
        <p style="text-align: right"><strong>عدد الطلبات هذا الأسبوع:</strong> {{ $currentWeekRequests }} طلب</p>
        <p style="text-align: right"><strong>عدد الطلبات الأسبوع الماضي:</strong> {{ $previousWeekRequests }} طلب</p>
    </div>
</body>
</html>
