<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Storage Manager') }}</title>

        {{-- @laravelPWA --}}

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        @yield('content')
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
