<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Storage Manager - Print</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        @media print {
            #nav--links * {
                display: none;
            }
        }
    </style>

    <script>window.print()</script>
</head>

<body class="py-4">
    <main>
        @yield('content')
    </main>
</body>

</html>
