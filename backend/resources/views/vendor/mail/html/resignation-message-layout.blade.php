@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<a href="https://app.ptc-ksa.net" style="display: inline-block;">
    <img src="{{ asset('/img/logox.png') }}" style="width:150px;" alt="شركة مركز احترافية التدريب">
</a>
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::resignation-footer')
© {{ date('Y') }} {{ config('app.developer-name') }}. @lang('words.all-rights-reserved')
@endcomponent
@endslot
@endcomponent
