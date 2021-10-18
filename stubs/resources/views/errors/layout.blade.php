<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
	<title>Error | {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="/storage/img/logo.svg">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>

<body>
	<div class="max-w-screen-md mx-auto my-20">
		<a href="/" class="block mb-4">
			<img src="/storage/img/logo-e.svg" alt="{{ config('app.name') }} - Error" class="w-60 mx-auto">
		</a>

		@isset($code)
			<div class="text-center text-4xl mb-2">
				Error <span class="font-bold">{{ $code }}</span>
			</div>
		@endif

		<div class="text-lg text-gray-500 text-center mb-4">
			{{ $message }}
		</div>

		<div class="text-center">
			<a class="py-2 px-4 w-full text-gray-800 font-medium rounded-md hover:bg-gray-200" href="/">
				Back to home
			</a>
		</div>
	</div>
</body>
</html>
