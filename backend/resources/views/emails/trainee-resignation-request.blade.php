@component('mail::message')
# طلب استقالة متدرب

تم استلام طلب استقالة من أحد المتدربين. يرجى مراجعة البيانات التالية:

## بيانات المتدرب

**الاسم:** {{ $trainee->name }}  
**رقم الهوية:** {{ $trainee->identity_number }}  
**البريد الإلكتروني:** {{ $trainee->email }}  
**رقم الجوال:** {{ $trainee->phone }}  

@if($contactPhone)
**رقم التواصل الإضافي:** {{ $contactPhone }}  
@endif

**الشركة:** {{ $trainee->company->name_ar ?? 'غير محدد' }}  
**تاريخ الطلب:** {{ now()->format('Y-m-d H:i:s') }}  

---

يرجى التواصل مع المتدرب في أقرب وقت ممكن لمعالجة طلب الاستقالة.

@component('mail::button', ['url' => 'mailto:' . $trainee->email])
رد على المتدرب
@endcomponent

شكراً،<br>
{{ config('app.name') }}
@endcomponent
