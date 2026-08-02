<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <style>
        body { background-color: #f0f0f0; text-align: right; }
        .card {
            padding: 40px;
            margin: 30px 60px;
            background-color: white;
            border-radius: 18px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }
        th { background-color: #ec5b5b; color: white; }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="card">
    <h2>دفع مكتمل عبر Noon بدون فاتورة/متدربة مطابقة</h2>

    <div class="warning">
        تم استلام إشعار دفع ناجح من Noon، لكن النظام لم يستطع ربطه بفاتورة أو متدربة.
        يرجى مراجعة Noon ولوحة التدقيق ومطابقة الدفع يدوياً.
    </div>

    <table>
        <tr>
            <th>الحقل</th>
            <th>القيمة</th>
        </tr>
        <tr>
            <td>سبب المشكلة</td>
            <td>{{ $payment['reason'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>رقم الطلب (orderId)</td>
            <td>{{ $payment['order_id'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>رقم طلب Noon</td>
            <td>{{ $payment['noon_order_id'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>مرجع الفاتورة (reference)</td>
            <td>{{ $payment['invoice_reference'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>المبلغ</td>
            <td>{{ $payment['amount'] ?? '—' }} {{ $payment['currency'] ?? '' }}</td>
        </tr>
        <tr>
            <td>حالة الدفع</td>
            <td>{{ $payment['payment_status'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>وقت الاستلام</td>
            <td>{{ $payment['received_at'] ?? '—' }}</td>
        </tr>
    </table>
</div>
</body>
</html>
