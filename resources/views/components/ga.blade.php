@if (!$disabled)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ ((array)$config->id)[0] }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    @foreach ((array)$config->id as $id)
    gtag('config', '{{ $id }}');
    @endforeach
    </script>
@endif

