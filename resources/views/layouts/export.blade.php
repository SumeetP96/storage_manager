<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Storage Manager - Print</title>

    <style>
        body {
            font-size: 0.8rem;
            font-family: sans-serif
        }

        header {
            font-size: 0.9rem;
            margin-bottom: 10px;
            font-weight: bold;
        }

        table {
            border-collapse: collapse;
            page-break-inside: auto;
            width: 100%
        }

        th, td {
            border: 0.5px solid grey;
            padding: 8px;
            vertical-align: top;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
        .font-bold { font-weight: bold; }
    </style>

    <script>window.print()</script>
</head>

<body class="py-4">
    <main>
        @yield('content')
    </main>
</body>

</html>
