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
<b>يوجد طلب انسحاب جديد</b>
<br/>
<br/>
_________________________________________________
<br/>
<br/>
رقم الطلب:&nbsp;&nbsp;<span dir="ltr">#{{ $withdrawal->number }}</span>
<br/>
<br/>

الشركة:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $withdrawal->trainee->company->name_ar }}<br/>
الاسم:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $withdrawal->trainee->name }}<br/>
الإيميل:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span dir="ltr">{{ $withdrawal->trainee->email }}</span><br/>
السبب:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span dir="ltr">{{ $withdrawal->reason ?: '-' }}</span><br/>

<br/>
_________________________________________________
<br/>
<br/>

رابط ملف المتدربة:<br/>
<a href="{{ route('back.trainees.show', $withdrawal->trainee->id) }}">{{ route('back.trainees.show', $withdrawal->trainee->id) }}</a>
<br/><br/>
رابط الطلبات:<br/>
<a href="{{ route('orders.withdrawal-requests.index') }}">{{ route('orders.withdrawal-requests.index') }}</a>

<br/>
<br/>
_________________________________________________
<br/>
<br/>
يتم إرسال هذا البريد الإلكتروني تلقائيًا من النظام. أنت تتلقى هذا البريد الإلكتروني لأنك مشترك في الإشعارات.
</body>
</html>
