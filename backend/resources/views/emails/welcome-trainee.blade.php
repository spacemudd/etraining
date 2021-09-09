@component('mail::message')
# @lang('words.hi-there')!

@lang('words.welcome-to-our-center-we-will-inform-you-when-your-application-is-approved')

<div style="text-align: center;">
    <img src="{{ url('/img/welcome-to-ptc.png') }}">
</div>

@component('mail::button', ['url' => url('/'), 'color' => 'primary'])
@lang('words.access-the-platform')
@endcomponent

@lang('words.thank-you-for-applying')

@lang('words.with-regards')
@endcomponent
