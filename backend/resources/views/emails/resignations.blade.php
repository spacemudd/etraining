@component('mail::resignation-message-layout')

{{-- السادة الكرام / {{ $resignation->company->name_ar }} --}}

@if ($resignation->trainees()->count() > 1)
بالإشارة إلى عقد التدريب نفيدكم بأن المتدربات أدناه:

@foreach ($resignation->trainees as $trainee)
- {{ $trainee->name }}
@endforeach

تم إيقافهم من البرنامج التدريبي بناءً على رغبتهن وطلبهن، مرفق لكم المستندات المستلمة من قبل المتدربات.
@else
نفيدكم بأن السيدة/ **{{ $resignation->trainees()->first()->name }}**

تم إيقافها من البرنامج التدريبي بناءً على رغبتها وطلبها، مرفق لكم المستندات المستلمة.
@endif

{{-- زر التأكيد اختياري إذا أردت إضافته --}}
{{-- 
@component('mail::button', ['url' => route('resignations.confirm-received', ['id' => $resignation->id])])
تأكيد استلام المستندات
@endcomponent 
--}}

مع تحياتنا،

@slot('footer')
    @component('mail::resignation-footer')
        البريد الإلكتروني مرسل عن طريق النظام الإلكتروني بشكل تلقائي من قبل المتدربة والمستندات المرفقة مرفوعة من قبلها. وفي حال وجود أي ملاحظة لا تترددوا بالتواصل معنا على البريد الإلكتروني المعتمد في العقد.
        <br/>
        © {{ date('Y') }} @lang('All rights reserved.')
    @endcomponent
@endslot
@endcomponent
