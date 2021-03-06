@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<a href="{{ config('app.url') }}" style="display: inline-block;">
    <img src="{{ asset('/img/logo-lg.png') }}" style="width:150px;" alt="PTC Logo">
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
@component('mail::footer')
© {{ date('Y') }} {{ config('app.developer-name') }}. @lang('words.all-rights-reserved')
@endcomponent
@endslot
@endcomponent
