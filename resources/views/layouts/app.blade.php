<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="author" content="Alex Aaqil">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('assets/images/default_image.jpg') }}" type="image/x-icon">

        @vite(['resources/css/icons/icons.css', 'resources/css/app.css', 'resources/js/app.js'])

        {{ $head ?? '' }}
    </head>
    <body>
        {{ $slot }}

        {{ $scripts ?? '' }}
        <script src="{{ asset('assets/js/alert.js') }}"></script>
    </body>
</html>
