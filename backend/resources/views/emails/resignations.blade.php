@component('mail::resignation-message-layout')

{{-- السادة الكرام / {{ $resignation->company->name_ar }} --}}

@php
    $traineeNames = $resignation->trainees->pluck('name')->filter()->implode('، ');
    $traineesCount = $resignation->trainees->count();
@endphp

@if ($traineesCount > 1)
نفيدكم بأنه تم تقديم طلب إيقاف البرنامج الخاص بالسيدات/ **{{ $traineeNames }}**

وذلك بناءً على رغبتهن وطلبهن.
@else
نفيدكم بأنه تم تقديم طلب إيقاف البرنامج الخاص بالسيدة/ **{{ $traineeNames }}**

وذلك بناءً على رغبتها وطلبها.
@endif

مرفق لسيادتكم المستندات الخاصة بذلك.

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
