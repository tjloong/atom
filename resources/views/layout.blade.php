<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 leading-relaxed">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-atom-seo />

    <link rel="shortcut icon" href="{{ asset('storage/img/logo.svg') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">

    @if (app()->isLocale('zh-my'))
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@100;300;400;500;700;900&display=swap">
    @else
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap">
    @endif

    @stack('styles')

    <x-atom-gtm/>
    <x-atom-ga/>
    <script src="https://unpkg.com/boxicons@2.0.9/dist/boxicons.js"></script>
    @stack('scripts')
</head>

<body class="font-sans leading-tight text-gray-800 antialiased">
@yield('content')
</body>
</html>