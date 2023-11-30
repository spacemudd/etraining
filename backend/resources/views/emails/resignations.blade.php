@component('mail::message')

السادة الكرام / {{ $resignation->company->name_ar }}

@if ($resignation->trainees()->count() > 1)
بالإشارة إلى عقد التدريب نفيدكم بأن المتدربات أدناه:

@foreach ($resignation->trainees as $trainee)
    - {{ $trainee->name }}
@endforeach

تم إيقافهم من البرنامج التدريبي بناء على رغبتهم وطلبهم، ومرفق لكم المستندات المستلمة من قبل المتدربات.

مع تحياتنا،
@else
بالإشارة إلى عقد التدريب نفيدكم بأن المتدربة / **{{ $resignation->trainees()->first()->name }}**

تم إيقافها من البرنامج التدريبي بناء على رغبتها وطلبها، ومرفق لكم المستندات المستلمة من قبل المتدربة.

مع تحياتنا،
@endif

    @slot('footer')
        @component('mail::footer')
            البريد الالكتروني مرسل عن طريق النظام الالكتروني بشكل تلقائي في حالة وجود اي ملاحظة لا تترددوا بالتواصل معنا على البريد الالكتروني المعتمد بدون اي مسؤولية قانونية على المرسل.
            <br/>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
