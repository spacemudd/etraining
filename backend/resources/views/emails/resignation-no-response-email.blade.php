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
<b>لا يوجد رد على الاستقالة من قبل العميل</b>
<br/>
<br/>
_________________________________________________
<br/>
<br/>
رقم الطلب:&nbsp;&nbsp;<span dir="ltr">#{{ $resignation->number }}</span>
<br/>
<br/>

الشركة:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $resignation->number->company->name_ar }}<br/>

<br/>
_________________________________________________
<br/>
<br/>

رابط الشركة:<br/>
<a href="{{ route('back.companies.show', $resignation->id) }}">
    {{ route('back.companies.show', $resignation->id) }}
</a>

<br/>
<br/>
_________________________________________________
<br/>
<br/>
يتم إرسال هذا البريد الإلكتروني تلقائيًا من النظام. أنت تتلقى هذا البريد الإلكتروني لأنك مشترك في الإشعارات.
</body>
</html>
