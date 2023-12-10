@component('mail::resignation-message-layout')

{{--السادة الكرام / {{ $resignation->company->name_ar }}--}}

@if ($resignation->trainees()->count() > 1)
بالإشارة إلى عقد التدريب نفيدكم بأن المتدربات أدناه:

@foreach ($resignation->trainees as $trainee)
    - {{ $trainee->name }}
@endforeach

تم إيقافهم من البرنامج التدريبي بناء على رغبتهم وطلبهم، ومرفق لكم المستندات المستلمة من قبل المتدربات.

مع تحياتنا،
@else
بالإشارة إلى عقد التدريب نفيدكم بأن المتدربة / **{{ $resignation->trainees()->first()->name }}**
x
تم إيقافها من البرنامج التدريبي بناء على رغبتها وطلبها، ومرفق لكم المستندات المستلمة من قبل المتدربة.

<br/>
<hr style="color:white;">
<br/>

يرجى الضغط أدناه لتأكيد استلامكم للمستندات المرفقة.

@component('mail::button', ['url' => route('resignations.confirm-received', ['id' => $resignation->id])])
تأكيد استلام المستندات
@endcomponent

مع تحياتنا،
@endif

    @slot('footer')
        @component('mail::resignation-footer')
            البريد الالكتروني مرسل عن طريق النظام الإلكتروني بشكل تلقائي من قبل المتدربة والمستندات المرفقة مرفوعة من قبلها وفي حالة وجود اي ملاحظة لا تترددوا بالتواصل معنا على البريد الالكتروني المعتمد في العقد بدون اي مسؤولية قانونية على شركة مركز احترافية التدريب.
            <br/>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
