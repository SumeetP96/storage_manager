<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Storage Manager Login</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
</head>

<body class="py-4 bg-light">
  <main>
    @yield('content')
  </main>
</body>

</html>
