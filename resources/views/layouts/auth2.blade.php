<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Storage Manager Change Password</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <style>
    body,
    html {
      height: 100%;
    }

    .login-wrapper {
      height: 100%;
      width: 100%;
      background-image: url({{ url('images/login_page/1.jpg')}});
      background-position: center;
      background-size: cover;
      background-repeat: no-repeat;
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <main style="padding-top: 6%">
      @yield('content')
    </main>
  </div>
</body>

</html>
