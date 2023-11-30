@component('mail::resignation-message-layout')

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
@endcomponent
