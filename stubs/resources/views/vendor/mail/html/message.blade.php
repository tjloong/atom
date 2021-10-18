@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img 
    src="{{ $logo ?? asset('storage/img/logo.png') }}"
    alt="{{ $logo_alt ?? config('app.name') }}"
    style="margin: 1.5rem 0; {{ isset($logo) ? 'width: 200px;' : 'width: 150px;' }}"
>
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
@isset($footer)
{{ $footer }}
@else
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endisset
@endcomponent
@endslot
@endcomponent
