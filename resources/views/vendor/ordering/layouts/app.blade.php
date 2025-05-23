<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="robots" content="noindex">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @livewireStyles

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div x-data
     @redirect-to.window="window.location.href = $event.detail.url"
     class="font-sans text-gray-900 dark:bg-gray-900 dark:text-white antialiased">
    {{ $slot }}
</div>


@livewireScripts
<script>
    window.addEventListener('redirectTo', event => {
        if (event.detail?.url) {
            window.location.href = event.detail.url;
        }
    });
</script>


</body>
</html>