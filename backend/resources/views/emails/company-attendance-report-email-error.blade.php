<html>
<head>
    <style>
        * {
            direction: rtl;
        }
    </style>
</head>
<body>
<br/>
<b>يوجد خطأ في ارسال الحضور والانصراف</b>
<br/>
<br/>
_________________________________________________
<br/>
<br/>
رقم الطلب:&nbsp;&nbsp;<span dir="ltr">#{{ $company_attendance_reports_email->report->number }}</span>
<br/>
<br/>

الشركة:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $company_attendance_reports_email->report->company->name_ar }}<br/>
الإيميل:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#ff0000;">{{ $company_attendance_reports_email->email }}</span><br/>
السبب:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="direction:ltr">{{ $company_attendance_reports_email->failed_reason }}</span><br/>

<br/>
<br/>s
<br/>
يتم إرسال هذا البريد الإلكتروني تلقائيًا من النظام. أنت تتلقى هذا البريد الإلكتروني لأنك مشترك في الإشعارات.
</body>
</html>
