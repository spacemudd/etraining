@component('mail::message')

# إشعار إجازة وضع

**السلام عليكم ورحمة الله وبركاته،**

نود إحاطتكم علماً بأن السيدة **{{ $trainee->name }}** قد تم منحها إجازة وضع اعتباراً من تاريخ **{{ \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d') }}** وحتى تاريخ **{{ \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d') }}**.

وبناءً على نظام التأمينات الاجتماعية واللوائح المعمول بها، نود إحاطتكم علماً بأن هذه الإجازة مدفوعة الأجر من التأمينات الاجتماعية، وبالتالي لا يُصرف للموظفة راتب عن فترة الإجازة المحددة أعلاه.

## تفاصيل الإجازة:
- **اسم الموظفة:** {{ $trainee->name }}
- **نوع الإجازة:** {{ $leave->leave_type }}
- **تاريخ بداية الإجازة:** {{ \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d') }}
- **تاريخ نهاية الإجازة:** {{ \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d') }}
- **تاريخ الطلب:** {{ \Carbon\Carbon::parse($leave->created_at)->format('Y-m-d H:i') }}

@if($leave->notes)
## ملاحظات إضافية:
{{ $leave->notes }}
@endif

---

**مع تحياتنا،**

**فريق إدارة التدريب**

@endcomponent
