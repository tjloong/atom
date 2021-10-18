<title>{{ $title }}</title>
<meta name="description" content="{{ strip_tags($config->description) }}">

@if ($disabled)
<meta name="robots" content="noindex">

@else
<meta property="og:locale" content="en_US">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $config->title }}">
<meta property="og:description" content="{{ strip_tags($config->description) }}">
<meta property="og:image" content="{{ $config->image }}">
<meta property="og:image:alt" content="{{ $config->title }}">
<meta property="og:site_name" content="{{ $config->title }}">

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{{ $config->title }}">
<meta name="twitter:description" content="{{ strip_tags($config->description) }}">
<meta name="twitter:image" content="{{ $config->image }}">
<meta name="twitter:image:alt" content="{{ $config->title }}">

<script type="application/ld+json">
@json($jsonld)
</script>

@if ($hreflang)
<link rel="alternate" href="{{ url()->current() }}" hreflang="{{ $hreflang }}" />
@endif

@if ($canonical)
<link rel="canonical" href="{{ $canonical }}" />
@endif
@endif